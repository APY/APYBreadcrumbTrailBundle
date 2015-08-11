<?php

namespace APY\BreadcrumbTrailBundle\Twig;


use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

/**
 * Class TitleExtension
 */
class TitleExtension extends \Twig_Extension
{
    /** @var Trail $_trail */
    private $_trail;

    public function __construct(Trail $trail)
    {
        $this->_trail = $trail;
    }

    public function getGlobals()
    {
        return array(
            'pageTitle' => $this->returnCurrentBreadcrumb()
        );
    }

    public function returnCurrentBreadcrumb()
    {
        $title = '';

        foreach($this->_trail as $breadcrumb) {
            $title = $breadcrumb->title;
        }

        return $title;
    }

    public function getName()
    {
        return 'pageTitleRender';
    }
}