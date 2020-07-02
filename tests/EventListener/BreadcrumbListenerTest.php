<?php

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\Annotation as APY;
use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BreadcrumbListenerTest extends BaseBundleTestCase
{
    public function controllerProvider(): array
    {
        require_once __DIR__.'/ProxyController.php';

        /**
         * @APY\Breadcrumb("name")
         */
        $closure = function () {
        };

        /**
         * @APY\Breadcrumb("name")
         */
        function controllerFunction() {
        }

        return [
            'Traditional' => [[new class {
                /**
                 * @APY\Breadcrumb("name")
                 */
                public function action()
                {
                }
            }, 'action'], 1],
            'Invokable' => [new class {
                /**
                 * @APY\Breadcrumb("name")
                 */
                public function __invoke()
                {
                }
            }, 1],
            'Private __invoke' => [new class {
                /**
                 * @APY\Breadcrumb("name")
                 */
                private function __invoke() // emits a WARNING
                {
                }
            }, 0],
            'Proxy' => [['\JMSSecurityExtraBundle\__CG__\APY\BreadcrumbTrailBundle\EventListener\ProxyController', 'action'], 1],
            // Reading annotations gets no results since it's wrapped inside a Closure
            'Anonymous function' => [$closure, 0],
            // Doctrine annotation reader does not read annotations from functions, so we don't handle it
            'Function' => ['APY\BreadcrumbTrailBundle\EventListener\controllerFunction', 0],
        ];
    }

    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }

    protected function setUp(): void
    {
        $this->addCompilerPass(
            new PublicServicePass('|^APY\\\\BreadcrumbTrailBundle\\\\EventListener\\\\BreadcrumbListener$|')
        );
        $kernel = $this->createKernel();
        $kernel->boot();
    }

    /**
     * @dataProvider controllerProvider
     *
     * @param callable $controller
     * @param int      $expectedCrumbs
     */
    public function testOnKernelController(callable $controller, int $expectedCrumbs)
    {
        $container = $this->getContainer();
        $trail = $container->get(Trail::class);
        $listener = $container->get(BreadcrumbListener::class);
        $eventClass = class_exists(ControllerEvent::class) ? ControllerEvent::class : FilterControllerEvent::class;
        $event = new $eventClass(
            $container->get('kernel'),
            $controller,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST
        );
        $listener->onKernelController($event);
        /** @var Breadcrumb[] $crumbs */
        $crumbs = iterator_to_array($trail->getIterator());
        $this->assertCount($expectedCrumbs, $crumbs);
        if ($expectedCrumbs > 0) {
            $this->assertArrayHasKey(0, $crumbs);
            $this->assertSame('name', $crumbs[0]->title);
        }
    }

    public function testAbstractController(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp(
            '/^Annotations from class "[\s\S]+" cannot be read as it is abstract.$/m'
        );
        $controller = ['APY\BreadcrumbTrailBundle\EventListener\AbstractController', 'action'];
        $this->testOnKernelController($controller, 0);
    }
}

abstract class AbstractController
{
    /**
     * @APY\Breadcrumb("name")
     */
    public static function action()
    {
    }
}

/**
 * @APY\Breadcrumb(template="template.twig")
 */
class ProxyController
{
    /**
     * @APY\Breadcrumb("name")
     */
    public static function action()
    {
    }
}
