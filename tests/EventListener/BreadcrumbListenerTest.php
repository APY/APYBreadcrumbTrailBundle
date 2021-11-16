<?php

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributes;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributesAndAnnotations;
use APY\BreadcrumbTrailBundle\MixedAnnotationWithAttributeBreadcrumbsException;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

class BreadcrumbListenerTest extends BaseBundleTestCase
{
    /** @var BreadcrumbListener */
    private $listener;

    /** @var Trail */
    private $breadcrumbTrail;

    /** @var AppKernel */
    private $kernel;

    protected function setUpTest(): void
    {
        // TODO rename this to setUp method once bumping PHP to supporting return type declarations
        $this->kernel = $this->createKernel();
        $this->kernel->boot();
        $this->listener = $this->getContainer()->get('apy_breadcrumb_trail.annotation.listener');
        $this->breadcrumbTrail = $this->getContainer()->get('apy_breadcrumb_trail');
    }

    public function testAnnotations()
    {
        $this->setUpTest();

        $controller = new ControllerWithAnnotations();
        $kernelEvent = $this->createControllerEvent($controller);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(3, $this->breadcrumbTrail);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testAttributes()
    {
        $this->setUpTest();

        $controller = new ControllerWithAttributes();
        $kernelEvent = $this->createControllerEvent($controller);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(3, $this->breadcrumbTrail);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMixingAnnotationsWithAttributesFails()
    {
        $this->setUpTest();
        $this->expectException(MixedAnnotationWithAttributeBreadcrumbsException::class);

        $controller = new ControllerWithAttributesAndAnnotations();
        $kernelEvent = $this->createControllerEvent($controller);
        $this->listener->onKernelController($kernelEvent);
    }

    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }

    /**
     * @return ControllerEvent|FilterControllerEvent
     */
    private function createControllerEvent($controller)
    {
        if (Kernel::MAJOR_VERSION <= 4) {
            return new FilterControllerEvent($this->kernel, [$controller, 'indexAction'], new Request(), HttpKernelInterface::MASTER_REQUEST);
        }

        return new ControllerEvent($this->kernel, [$controller, 'indexAction'], new Request(), HttpKernelInterface::MASTER_REQUEST);
    }
}
