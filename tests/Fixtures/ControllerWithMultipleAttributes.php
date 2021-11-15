<?php

namespace APY\BreadcrumbTrailBundle\Fixtures;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Breadcrumb("first-breadcrumb")
 * @Breadcrumb("second-breadcrumb")
 */
class ControllerWithMultipleAttributes extends AbstractController
{

    /**
     * @Breadcrumb("third-breadcrumb")
     */
    public function annotationOnlyAction()
    {
        return [];
    }

    #[Breadcrumb(title: 'third-breadcrumb')]
    public function attributeOnlyAction()
    {
        return [];
    }

    /**
     * @Breadcrumb("third-breadcrumb")
     */
    #[Breadcrumb(title: 'third-breadcrumb')]
    public function mixedAction()
    {
        return [];
    }
}
