<?php

namespace APY\BreadcrumbTrailBundle;

use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(APYBreadcrumbTrailBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testServicesAreRegisteredToContainer()
    {
        $container = self::bootKernel()->getContainer();

        $this->assertTrue($container->has('apy_breadcrumb_trail'));
        $this->assertTrue($container->has('apy_breadcrumb_trail.annotation.listener'));
        $this->assertTrue($container->hasParameter('apy_breadcrumb_trail.template'));
    }
}
