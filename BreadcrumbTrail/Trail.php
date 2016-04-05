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

use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var ContainerInterface container
     */
    private $container;

    /**
     * @var string Template to render the breadcrumb trail
     */
    private $template;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $router URL generator class
     */
    public function __construct(UrlGeneratorInterface $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
        $this->breadcrumbs = new \SplObjectStorage();
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add breadcrumb
     *
     * @param mixed   $breadcrumb_or_title  A Breadcrumb instance or the title of the breadcrumb
     * @param string  $routeName            The name of the route
     * @param mixed   $routeParameters      An array of parameters for the route
     * @param Boolean $routeAbsolute        Whether to generate an absolute URL
     * @param integer $position             Position of the breadcrumb (default = 0)
     * @param mixed   $attributes           Additional attributes for the breadcrumb
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return self
     */
    public function add($breadcrumb_or_title, $routeName = null, $routeParameters = array(), $routeAbsolute = UrlGeneratorInterface::ABSOLUTE_PATH, $position = 0, $attributes = array())
    {
        if ($breadcrumb_or_title === null) {
            return $this->reset();
        }

        if ($breadcrumb_or_title instanceof Breadcrumb) {
            $breadcrumb = $breadcrumb_or_title;
        } else {
            if (!is_string($breadcrumb_or_title)) {
                throw new \InvalidArgumentException('The title of a breadcrumb must be a string.');
            }

            if ($this->container->has('request_stack')) {
                $request = $this->container->get('request_stack')->getCurrentRequest();
            } else {
                // For Symfony < 2.4
                $request = $this->container->get('request', ContainerInterface::NULL_ON_INVALID_REFERENCE);
            }

            if ($request !== null) {
                preg_match_all('#\{(?P<variable>\w+).?(?P<function>([\w\.])*):?(?P<parameters>(\w|,| )*)\}#', $breadcrumb_or_title, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $varName    = $match['variable'][0];
                    $functions  = $match['function'][0] ? explode('.', $match['function'][0]) : array();
                    $parameters = $match['parameters'][0] ? explode(',', $match['parameters'][0]) : array();
                    $nbCalls    = count($functions);

                    if($request->attributes->has($varName)) {
                        $object = $request->attributes->get($varName);

                        if(empty($functions)) {
                            $objectValue = (string) $object;
                        }
                        else {
                            foreach ($functions AS $f => $function) {

                                # While this is not the last function, call the chain
                                if ($f < $nbCalls - 1) {
                                    if(is_callable(array($object, $fullFunctionName = 'get'.$function))
                                        || is_callable(array($object, $fullFunctionName = 'has'.$function))
                                        || is_callable(array($object, $fullFunctionName = 'is'.$function))) {
                                        $object = call_user_func(array($object, $fullFunctionName));
                                    }
                                    else {
                                        throw new \RuntimeException(sprintf('"%s" is not callable.', join('.', array_merge([$varName], $functions))));
                                    }
                                }

                                # End of the chain: call the method
                                else {
                                    if(is_callable(array($object, $fullFunctionName = 'get'.$function))
                                        || is_callable(array($object, $fullFunctionName = 'has'.$function))
                                        || is_callable(array($object, $fullFunctionName = 'is'.$function))) {
                                        $objectValue = call_user_func_array(array($object, $fullFunctionName),$parameters);
                                    }
                                    else {
                                        throw new \RuntimeException(sprintf('"%s" is not callable.', join('.', array_merge([$varName], $functions))));
                                    }
                                }
                            }
                        }

                        $breadcrumb_or_title = str_replace($match[0][0], $objectValue, $breadcrumb_or_title);
                    }
                }

                foreach ($routeParameters as $key => $value) {
                    if (is_numeric($key)) {
                        $routeParameters[$value] = $request->get($value);
                        unset($routeParameters[$key]);
                    } else {
                        if (preg_match_all('#\{(?P<variable>\w+).?(?P<function>([\w\.])*):?(?P<parameters>(\w|,| )*)\}#', $value, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {

                            foreach ($matches AS $match) {

                                $varName    = $match['variable'][0];
                                $functions  = $match['function'][0] ? explode('.', $match['function'][0]) : array();
                                $parameters = $match['parameters'][0] ? explode(',', $match['parameters'][0]) : array();
                                $nbCalls    = count($functions);

                                if ($request->attributes->has($varName)) {
                                    $object = $request->attributes->get($varName);

                                    if (empty($functions)) {
                                        $objectValue = (string) $object;
                                    }
                                    else {
                                        foreach ($functions AS $f => $function) {

                                            # While this is not the last function, call the chain
                                            if ($f < $nbCalls - 1) {
                                                if (is_callable(array($object, $fullFunctionName = 'get' . $function))
                                                    || is_callable(array($object, $fullFunctionName  = 'has' . $function))
                                                    || is_callable(array($object, $fullFunctionName  = 'is' . $function))
                                                ) {
                                                    $object = call_user_func(array($object, $fullFunctionName));
                                                }
                                                else {
                                                    throw new \RuntimeException(sprintf('"%s" is not callable.', join('.', array_merge([$varName], $functions))));
                                                }
                                            }

                                            # End of the chain: call the method
                                            else {

                                                if (is_callable(array($object, $fullFunctionName = 'get' . $function))
                                                    || is_callable(array($object, $fullFunctionName  = 'has' . $function))
                                                    || is_callable(array($object, $fullFunctionName  = 'is' . $function))
                                                ) {
                                                    $objectValue = call_user_func_array(array($object, $fullFunctionName), $parameters);
                                                }
                                                else {
                                                    throw new \RuntimeException(sprintf('"%s" is not callable.', join('.', array_merge([$varName], $functions))));
                                                }
                                            }
                                        }
                                    }

                                    $routeParameter        = str_replace($match[0][0], $objectValue, $value);
                                    $routeParameters[$key] = $routeParameter;
                                }


                            }
                        } elseif (preg_match('#^\{(?P<parameter>\w+)\}$#', $value, $matches)) {
                            $routeParameters[$key] = $request->get($matches['parameter']);
                        }
                    }
                }
            }

            $url = null;
            if ( !is_null($routeName) ) {
                $referenceType = $routeAbsolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::RELATIVE_PATH;
                $url = $this->router->generate($routeName, $routeParameters, $referenceType);
            }

            $breadcrumb = new Breadcrumb($breadcrumb_or_title, $url, $attributes);
        }

        if (!is_int($position)) {
            throw new \InvalidArgumentException('The position of a breadcrumb must be an integer.');
        }

        if ($position == 0 || $position > $this->breadcrumbs->count()) {
            $this->breadcrumbs->attach($breadcrumb);
        } else {
            $this->insert($breadcrumb, $position);
        }

        return $this;
    }

    private function insert($breadcrumb, $position)
    {
        if ($position < 0) {
            $position += $this->breadcrumbs->count();
        } else { // $position >= 1
            $position--;
        }

        $breadcrumbs = new \SplObjectStorage();
        $breadcrumbs->addAll($this->breadcrumbs);
        $this->breadcrumbs->removeAll($this->breadcrumbs);

        $breadcrumbs->rewind();
        while ($breadcrumbs->valid()) {
            if (max(0, $position) == $breadcrumbs->key()) {
                $this->breadcrumbs->attach($breadcrumb);
            }

            $this->breadcrumbs->attach($breadcrumbs->current());
            $breadcrumbs->next();
        }
    }

    /**
     * Reset the trail
     *
     * @return self
     */
    public function reset()
    {
        $this->breadcrumbs->removeAll($this->breadcrumbs);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->breadcrumbs->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->breadcrumbs;
    }
}
