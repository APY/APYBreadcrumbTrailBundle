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

use APY\BreadcrumbTrailBundle\InvalidBreadcrumbException;
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
     * @var string
     */
    private $type;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param Trail $breadcrumbTrail An Trail instance
     * @param string $type Load Annotation, Attribute or both
     */
    public function __construct(Reader $reader, Trail $breadcrumbTrail, string $type)
    {
        $this->reader = $reader;
        $this->type = $type;
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
            $classAnnotations = $this->shouldLoadAnnotations() ? $this->reader->getClassAnnotations($class) : [];
            $classAttributes = $this->shouldLoadAttributes() ? $this->getAttributes($class): [];
            if (count($classAttributes) > 0 && count($classAnnotations) > 0)
                throw new InvalidBreadcrumbException($class->getName());

            $this->addBreadcrumbsFromAnnotations(array_merge($classAnnotations, $classAttributes));

            // Annotations from method
            $method = $class->getMethod($controller[1]);

            $methodAnnotations = $this->shouldLoadAnnotations() ? $this->reader->getMethodAnnotations($method) : [];
            $methodAttributes = $this->shouldLoadAttributes() ? $this->getAttributes($method) : [];
            if (count($methodAnnotations) > 0 && count($methodAttributes) > 0)
                throw new InvalidBreadcrumbException($class->getName(), $method->getName());

            $this->addBreadcrumbsFromAnnotations(array_merge($methodAnnotations, $methodAttributes));
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

    private function shouldLoadAnnotations(): bool
    {
        return in_array($this->type, ["annotation", "both"]);
    }
    private function shouldLoadAttributes(): bool
    {
        return in_array($this->type, ["attribute", "both"]);
    }

    /**
     * @param \ReflectionClass|\ReflectionMethod $reflected
     * @return list<Breadcrumb>
     */
    private function getAttributes($reflected): array
    {
        if (\PHP_VERSION_ID < 80000) {
            return [];
        }

        $attributes = [];
        foreach ($reflected->getAttributes(Breadcrumb::class) as $reflectionAttribute) {
            $attributes[] = $reflectionAttribute->newInstance();
        }

        return $attributes;
    }
}
