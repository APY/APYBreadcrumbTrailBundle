<?php

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\Annotation as APY;
use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Breadcrumb;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BreadcrumbListenerTest extends BaseBundleTestCase
{
    public function controllerProvider()
    {
        return [
            'Traditional' => [[new class {
                /**
                 * @APY\Breadcrumb("name")
                 */
                public function action()
                {
                }
            }, 'action']],
            'Invokable' => [new class {
                /**
                 * @APY\Breadcrumb("name")
                 */
                public function __invoke()
                {
                }
            }],
        ];
    }

    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }

    protected function setUp(): void
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/../services.xml');
        $kernel->addBundle(FrameworkBundle::class);
        $kernel->boot();
    }

    /**
     * @dataProvider controllerProvider
     *
     * @param callable $controller
     */
    public function testInvokableController($controller)
    {
        $container = $this->getContainer();
        /** @var BreadcrumbListener $listener */
        $trail = $container->get('APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail');
        $listener = $container->get('apy_breadcrumb_trail.annotation.listener');
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
        $this->assertCount(1, $crumbs);
        $this->assertArrayHasKey(0, $crumbs);
        $this->assertSame('name', $crumbs[0]->title);
    }
}
