<?php

namespace APY\BreadcrumbTrailBundle\Fixtures;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\Annotation\ResetBreadcrumbTrail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Breadcrumb(title: 'first-breadcrumb')]
class ResetTrailAttribute extends AbstractController
{
    #[ResetBreadcrumbTrail]
    #[Breadcrumb(title: 'first-breadcrumb-again')]
    public function indexAction()
    {
        return [];
    }
}
