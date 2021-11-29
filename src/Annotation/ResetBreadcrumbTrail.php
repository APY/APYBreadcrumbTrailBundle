<?php

namespace APY\BreadcrumbTrailBundle\Annotation;

/**
 * Resets the breadcrumb trail. Can be applied on controller classes, callables, invokables and action methods.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class ResetBreadcrumbTrail
{
}
