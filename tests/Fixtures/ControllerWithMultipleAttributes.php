<?php

namespace APY\BreadcrumbTrailBundle\Fixtures;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Breadcrumb("first-breadcrumb")
 * @Breadcrumb("second-breadcrumb")
 */
#[Breadcrumb(title: 'first-breadcrumb')]
#[Breadcrumb(title: 'second-breadcrumb')]
class ControllerWithMultipleAttributes extends AbstractController
{
    /**
     * @Breadcrumb("third-breadcrumb")
     */
    #[Breadcrumb(title: 'third-breadcrumb')]
    public function indexAction()
    {
        return [];
    }
}
