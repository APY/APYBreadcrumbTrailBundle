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

use APY\BreadcrumbTrailBundle\MixedAnnotationWithAttributeBreadcrumbsException;
use Doctrine\Common\Annotations\Reader;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
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
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent|\Symfony\Component\HttpKernel\Event\ControllerEvent  $event
     */
    public function onKernelController(KernelEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
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
            $classBreadcrumbs = $this->reader->getClassAnnotations($class);
            if ($this->supportsLoadingAttributes()) {
                $classAttributeBreadcrumbs = $this->getAttributes($class);
                if (count($classBreadcrumbs) > 0) {
                    trigger_deprecation('apy/breadcrumb-bundle', '1.7', 'Please replace the annotations in "%s" with attributes. Adding Breadcrumbs via annotations is deprecated and will be removed in v2.0, but luckily your platform supports using Attributes.', $class->name);
                }
                if (count($classAttributeBreadcrumbs) > 0) {
                    if (count($classBreadcrumbs) > 0) {
                        throw MixedAnnotationWithAttributeBreadcrumbsException::forClass($class->name);
                    }
                    $classBreadcrumbs = $classAttributeBreadcrumbs;
                }
            }
            $this->addBreadcrumbsToTrail($classBreadcrumbs);

            // Annotations from method
            $method = $class->getMethod($controller[1]);
            $methodBreadcrumbs = $this->reader->getMethodAnnotations($method);
            if ($this->supportsLoadingAttributes()) {
                $methodAttributeBreadcrumbs = $this->getAttributes($method);
                if (count($methodBreadcrumbs) > 0) {
                    trigger_deprecation('apy/breadcrumb-bundle', '1.7', 'Please replace the annotations in "%s" with attributes. Adding Breadcrumbs via annotations is deprecated and will be removed in v2.0, but luckily your platform supports using Attributes.', $class->name.'::'.$method->name);
                }
                if (count($methodAttributeBreadcrumbs) > 0) {
                    if (count($methodBreadcrumbs) > 0) {
                        throw MixedAnnotationWithAttributeBreadcrumbsException::forClassMethod($class->name, $method->name);
                    }
                    $methodBreadcrumbs = $methodAttributeBreadcrumbs;
                }
            }
            $this->addBreadcrumbsToTrail($methodBreadcrumbs);
        }
    }

    /**
     * @param array $annotations Array of Breadcrumb annotations
     */
    private function addBreadcrumbsToTrail(array $annotations)
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

    private function supportsLoadingAttributes(): bool
    {
        return \PHP_VERSION_ID >= 80000;
    }

    /**
     * @param \ReflectionClass|\ReflectionMethod $reflected
     * @return array<Breadcrumb>
     */
    private function getAttributes($reflected): array
    {
        if ($this->supportsLoadingAttributes() === false) {
            throw new \RuntimeException('Detected an attempt on getting attributes while your version of PHP does not support this.');
        }

        $attributes = [];
        foreach ($reflected->getAttributes(Breadcrumb::class) as $reflectionAttribute) {
            $attributes[] = $reflectionAttribute->newInstance();
        }

        return $attributes;
    }
}
