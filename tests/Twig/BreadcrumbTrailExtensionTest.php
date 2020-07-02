<?php

namespace APY\BreadcrumbTrailBundle\Twig;

use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Twig\Environment;

/**
 * @coversDefaultClass \APY\BreadcrumbTrailBundle\Twig\BreadcrumbTrailExtension
 */
class BreadcrumbTrailExtensionTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }

    protected function setUp(): void
    {
        $this->addCompilerPass(new PublicServicePass('|^twig$|'));

        $kernel = $this->createKernel();
        $kernel->addBundle(TwigBundle::class);
        $kernel->boot();
    }

    public function testTwigFunctionGetsRegistered()
    {
        $container = $this->getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');

        self::assertNotNull(
            $twig->getFunction('apy_breadcrumb_trail_render')
        );
    }
}
