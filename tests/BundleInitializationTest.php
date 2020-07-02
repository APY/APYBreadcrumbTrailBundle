<?php

namespace APY\BreadcrumbTrailBundle;

use Nyholm\BundleTest\BaseBundleTestCase;

class BundleInitializationTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }

    public function testServicesAreRegistered()
    {
        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('apy_breadcrumb_trail'));
        $this->assertTrue($container->hasParameter('apy_breadcrumb_trail.template'));
    }
}
