<?php

namespace APY\BreadcrumbTrailBundle\BreadcrumbTrail;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TrailTest extends TestCase
{
    public function testRenderSimpleValueObjectValueInBreadcrumbTitle()
    {
        $router = $this->createMock(UrlGeneratorInterface::class);
        $requestStack = new RequestStack();

        $expected = 'sample-name';
        $requestStack->push(new Request([], [], [
            'user' => new User($expected),
        ]));

        $trail = new Trail($router, $requestStack);
        $trail->add('{user.name}');

        $iterator = $trail->getIterator();
        self::assertCount(1, $iterator);

        /** @var Breadcrumb $breadcrumb */
        $breadcrumb = $iterator->current();
        self::assertEquals($expected, $breadcrumb->title);
    }
}

final class User
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
