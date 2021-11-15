<?php

namespace APY\BreadcrumbTrailBundle;


class InvalidBreadcrumbException extends \Exception
{
    public function __construct(string $class, string $method = null)
    {
        $target = $class;
        if ($method)
            $target .= "::$method()";
        else
            $target .= "::class";

        parent::__construct("The breadcrumb on $target is invalid. You can only use Attribute or Annotation, not both.");
    }
}