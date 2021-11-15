<?php

namespace APY\BreadcrumbTrailBundle\Fixtures;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Breadcrumb(title: 'first-breadcrumb')]
#[Breadcrumb(title: 'second-breadcrumb')]
class ControllerWithAttributes extends AbstractController
{
    #[Breadcrumb(title: 'third-breadcrumb')]
    public function indexAction()
    {
        return [];
    }
}
