<?php

namespace APY\BreadcrumbTrailBundle\Annotation;

use PHPUnit\Framework\TestCase;

class BreadcrumbTest extends TestCase
{
    public function testConstructWithSimpleTitle()
    {
        $expected = 'title-of-the-breadcrumb';
        $breadcrumb = new Breadcrumb($expected);

        self::assertEquals($expected, $breadcrumb->getTitle());
    }

    public function testConstructWithLegacyArray()
    {
        $expected = 'title-of-the-breadcrumb';
        $breadcrumb = new Breadcrumb(['title' => $expected]);

        self::assertEquals($expected, $breadcrumb->getTitle());
    }

    public function testConstructWithLegacyRouteArray()
    {
        $expected = 'title-of-the-breadcrumb';
        $expectedRouteAbsolute = true;
        $breadcrumb = new Breadcrumb([
            'title' => $expected,
            'route' => ['absolute' => $expectedRouteAbsolute],
        ]);

        self::assertEquals($expected, $breadcrumb->getTitle());
        self::assertEquals($expectedRouteAbsolute, $breadcrumb->getRouteAbsolute());
    }

    public function testValueWillGetInterpretedAsTitle()
    {
        $expected = 'title-of-the-breadcrumb';
        $breadcrumb = new Breadcrumb(['value' => $expected]);

        self::assertEquals($expected, $breadcrumb->getTitle());
    }

    public function testUnnamedWillGetInterpretedAsValueForTitle()
    {
        $expected = 'title-of-the-breadcrumb';
        $breadcrumb = new Breadcrumb([$expected, 'route' => ['name' => 'my_route']]);

        self::assertEquals($expected, $breadcrumb->getTitle());
    }

    /**
     * @deprecated passing empty constructor is deprecated since 1.8. ResetBreadcrumbTrail attribute should be used instead. Will throw exception in 2.0.
     */
    public function testConstructorWithoutArgumentsIsAllowedForResettingTrail()
    {
        $expected = null;
        $breadcrumb = new Breadcrumb();

        self::assertEquals($expected, $breadcrumb->getTitle());
    }
}
