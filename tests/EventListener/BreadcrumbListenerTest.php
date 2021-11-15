<?php

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributes;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributesAndAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithMultipleAttributes;
use APY\BreadcrumbTrailBundle\InvalidBreadcrumbException;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
        $kernelEvent = new ControllerEvent($this->kernel, [$controller, 'indexAction'], new Request(), HttpKernelInterface::MASTER_REQUEST);
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
        $kernelEvent = new ControllerEvent($this->kernel, [$controller, 'indexAction'], new Request(), HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(3, $this->breadcrumbTrail);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMixingAnnotationsWithAttributesFails()
    {
        $this->setUpTest();
        $this->expectException(InvalidBreadcrumbException::class);

        $controller = new ControllerWithAttributesAndAnnotations();
        $kernelEvent = new ControllerEvent($this->kernel, [$controller, 'indexAction'], new Request(), HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(4, $this->breadcrumbTrail);
    }

    protected function getBundleClass()
    {
        return APYBreadcrumbTrailBundle::class;
    }
}
