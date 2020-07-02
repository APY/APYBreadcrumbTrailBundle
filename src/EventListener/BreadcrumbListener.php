<?php

/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\EventListener;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BreadcrumbListener
{
    /**
     * @var Reader An Reader instance
     */
    protected $reader;

    /**
     *
     * @var Trail An Trail instance
     */
    protected $breadcrumbTrail;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param Trail $breadcrumbTrail An Trail instance
     */
    public function __construct(Reader $reader, Trail $breadcrumbTrail)
    {
        $this->reader = $reader;
        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    /**
     * @param FilterControllerEvent|ControllerEvent $event
     *
     * @throws \ReflectionException
     */
    public function onKernelController(KernelEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            // Doctrine annotation reader can not work with functions
            // Closures don't allow reading annotations of wrapped functions
            if (is_string($controller) && function_exists($controller) || $controller instanceof \Closure) {
                return;
            }
            $controller = [$controller, '__invoke'];
            if (!is_callable($controller)) {
                return;
            }
        }

        // Annotations from class
        $class = new \ReflectionClass($controller[0]);

        // Manage JMSSecurityExtraBundle proxy class
        if (false !== $className = $this->getRealClass($class->getName())) {
            $class = new \ReflectionClass($className);
        }

        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $this->breadcrumbTrail->reset();

            // Annotations from class
            $this->addBreadcrumbsFromAnnotations($this->reader->getClassAnnotations($class));

            // Annotations from method
            $method = $class->getMethod($controller[1]);
            $this->addBreadcrumbsFromAnnotations($this->reader->getMethodAnnotations($method));
        }
    }

    /**
     * Add Breadcrumb from annotations to the trail.
     *
     * @param array $annotations Array of Breadcrumb annotations
     */
    private function addBreadcrumbsFromAnnotations(array $annotations)
    {
        // requirements (@Breadcrumb)
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Breadcrumb) {
                $template = $annotation->getTemplate();
                $title = $annotation->getTitle();

                if ($template != null) {
                    $this->breadcrumbTrail->setTemplate($template);
                    if ($title === null) {
                        continue;
                    }
                }

                $this->breadcrumbTrail->add(
                    $title,
                    $annotation->getRouteName(),
                    $annotation->getRouteParameters(),
                    $annotation->getRouteAbsolute(),
                    $annotation->getPosition(),
                    $annotation->getAttributes()
                );
            }
        }
    }

    private function getRealClass($className)
    {
        if (false === $pos = strrpos($className, '\\__CG__\\')) {
            return false;
        }

        return substr($className, $pos + 8);
    }
}
