<?php

namespace APY\BreadcrumbTrailBundle;


class MixedAnnotationWithAttributeBreadcrumbsException extends \LogicException
{
    private function __construct(string $class, string $method = null)
    {
        $target = $class;
        if ($method) {
            $target .= "::$method()";
        } else {
            $target .= "::class";
        }

        parent::__construct("A mix of Breadcrumb attributes and annotations is not allowed. Please upgrade all annotations on class `{$target}` to attributes.");
    }

    public static function forClass(string $class)
    {
        return new self($class);
    }

    public static function forClassMethod(string $class, string $method)
    {
        return new self($class, $method);
    }
}
