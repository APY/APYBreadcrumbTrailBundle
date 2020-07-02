<?php

namespace APY\BreadcrumbTrailBundle;

use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\DependencyInjection\APYBreadcrumbTrailExtension;
use APY\BreadcrumbTrailBundle\EventListener\BreadcrumbListener;
use APY\BreadcrumbTrailBundle\Twig\BreadcrumbTrailExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class ExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new APYBreadcrumbTrailExtension()
        ];
    }

    public function testContainerHasExtension()
    {
        $this->load();
        $this->assertContainerBuilderHasService(Trail::class);
        $this->assertContainerBuilderHasService(BreadcrumbListener::class);
    }
}
