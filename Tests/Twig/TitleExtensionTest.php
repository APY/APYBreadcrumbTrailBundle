<?php

namespace APY\BreadcrumbTrailBundle\Tests\Twig;


use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use APY\BreadcrumbTrailBundle\Twig\TitleExtension;

/**
 * Class TitleExtensionTest
 */
class TitleExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var TitleExtension $_title */
    private $_title;

    public function setUp()
    {
        $url = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $trail = new Trail($url, $container);
        $trail->add('Test', NULL, array());
        $this->_title = new TitleExtension($trail);
    }

    public function testGetGlobals()
    {
        $actual = $this->_title->getGlobals();

        $expected = array(
            'pageTitle' =>  $this->_title->returnCurrentBreadcrumb()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testReturnPath()
    {
        $this->assertEquals('Test', $this->_title->returnCurrentBreadcrumb());
    }

    public function testGetName()
    {
        $this->assertEquals('pageTitleRender', $this->_title->getName());
    }

}