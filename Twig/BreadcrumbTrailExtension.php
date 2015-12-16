<?php

/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an extension for Twig to output breadcrumbs
 */
class BreadcrumbTrailExtension extends \Twig_Extension
{
    /**
     *
     * @var ContainerInterface An ContainerInterface instance
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction("apy_breadcrumb_trail_render", array($this, "renderBreadcrumbTrail"), array("is_safe" => array("html"))),
        );
    }

    /**
     * Renders the breadcrumb trail in a list
     *
     * @return string
     */
    public function renderBreadcrumbTrail($template = null)
    {
        $breadcrumbs = $this->container->get("apy_breadcrumb_trail");
        return $this->container->get("templating")->render(
                $template == null ? $breadcrumbs->getTemplate() : $template,
                array( 'breadcrumbs' => $breadcrumbs )
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "breadcrumbtrail";
    }
}
