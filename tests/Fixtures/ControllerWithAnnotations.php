<?php

namespace APY\BreadcrumbTrailBundle\Fixtures;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Breadcrumb("first-breadcrumb")
 * @Breadcrumb("second-breadcrumb")
 */
class ControllerWithAnnotations extends AbstractController
{
    /**
     * @Breadcrumb("third-breadcrumb")
     */
    public function indexAction()
    {
        return [];
    }
}
