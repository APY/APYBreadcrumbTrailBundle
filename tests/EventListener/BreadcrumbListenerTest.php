<?php

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributes;
use APY\BreadcrumbTrailBundle\Fixtures\ControllerWithAttributesAndAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\InvokableControllerWithAnnotations;
use APY\BreadcrumbTrailBundle\Fixtures\ResetTrailAttribute;
use APY\BreadcrumbTrailBundle\MixedAnnotationWithAttributeBreadcrumbsException;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class BreadcrumbListenerTest extends KernelTestCase
{
    /** @var BreadcrumbListener */
    private $listener;

    /** @var Trail */
    private $breadcrumbTrail;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(APYBreadcrumbTrailBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    protected function setUpTest(): void
    {
        $kernel = self::bootKernel();
        $this->listener = $kernel->getContainer()->get('apy_breadcrumb_trail.annotation.listener');
        $this->breadcrumbTrail = $kernel->getContainer()->get('apy_breadcrumb_trail');
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

    public function testInvokableController()
    {
        $this->setUpTest();

        $controller = new InvokableControllerWithAnnotations();
        $kernelEvent = $this->createControllerEvent($controller);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(3, $this->breadcrumbTrail);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testResetTrailAttribute()
    {
        $this->setUpTest();

        $controller = new ResetTrailAttribute();
        $kernelEvent = $this->createControllerEvent($controller);
        $this->listener->onKernelController($kernelEvent);

        self::assertCount(1, $this->breadcrumbTrail);
    }

    protected function getBundleClass(): string
    {
        return APYBreadcrumbTrailBundle::class;
    }

    /**
     * @return ControllerEvent|FilterControllerEvent
     */
    private function createControllerEvent($controller)
    {
        $callable = \is_callable($controller) ? $controller : [$controller, 'indexAction'];
        if (Kernel::MAJOR_VERSION <= 4) {
            return new FilterControllerEvent(self::$kernel, $callable, new Request(), HttpKernelInterface::MASTER_REQUEST);
        }

        return new ControllerEvent(self::$kernel, $callable, new Request(), HttpKernelInterface::MASTER_REQUEST);
    }
}
