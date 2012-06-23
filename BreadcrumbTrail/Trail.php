<?php

/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\BreadcrumbTrail;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Trail implements \IteratorAggregate, \Countable
{
    /**
     * @var Breadcrumb[] Array of breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var UrlGeneratorInterface URL generator class
     */
    private $router;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $router URL generator class
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
        $this->breadcrumbs = new \SplObjectStorage();
    }

    /**
     * Add breadcrumb
     *
     * @param mixed   $breadcrumb_or_title  A Breadcrumb instance or the title of the breadcrumb
     * @param string  $routeName            The name of the route
     * @param mixed   $routeParameters      An array of parameters for the route
     * @param Boolean $routeAbsolute        Whether to generate an absolute URL
     * @return self
     */
    function add($breadcrumb_or_title, $routeName = null, $routeParameters = array(), $routeAbsolute = false)
    {
        if ($breadcrumb_or_title instanceof Breadcrumb) {
            $this->breadcrumbs->attach($breadcrumb_or_title);
        } else {
            if (!is_string($breadcrumb_or_title)) {
                throw new \InvalidArgumentException('The title of a breadcrumb must be a string.');
            }

            $url = null;
            if ( !is_null($routeName) ) {
                $url = $this->router->generate($routeName, $routeParameters, $routeAbsolute);
            }

            $this->breadcrumbs->attach(new Breadcrumb($breadcrumb_or_title, $url));
        }

        return $this;
    }

    /**
     * Reset the trail
     *
     * @return self
     */
    public function reset() {
        $this->breadcrumbs->removeAll($this->breadcrumbs);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function count() {
        return $this->breadcrumbs->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() {
        return $this->breadcrumbs;
    }
}
