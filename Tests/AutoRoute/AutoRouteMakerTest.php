<?php

namespace Symfony\Cmf\Bundle\RoutingAutoBundle\Tests\AutoRoute;

use Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\AutoRouteMaker;
use Doctrine\Common\Collections\ArrayCollection;

class AutoRouteMakerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->dm = $this->getMockBuilder(
            'Doctrine\ODM\PHPCR\DocumentManager'
        )->disableOriginalConstructor()->getMock();

        $this->uow = $this->getMockBuilder(
            'Doctrine\ODM\PHPCR\UnitOfWork'
        )->disableOriginalConstructor()->getMock();

        $this->metadata = $this->getMockBuilder(
            'Doctrine\ODM\PHPCR\Mapping\ClassMetadata'
        )->disableOriginalConstructor()->getMock();

        $this->arm = new AutoRouteMaker($this->dm);
        $this->doc = new \stdClass;

        $this->autoRoute1 = $this->getMock(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\Document\AutoRoute'
        );
        $this->autoRoute2 = $this->getMock(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\Document\AutoRoute'
        );
        $this->nonAutoRoute = new \stdClass;

        $this->autoRouteStack =  $this->getMockBuilder(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\AutoRouteStack'
        )->disableOriginalConstructor()->getMock();

        $this->builderContext = $this->getMock(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\BuilderContext'
        );
    }

    public function testCreateOrUpdateAutoRouteForExisting()
    {
        $this->dm->expects($this->once())
            ->method('getReferrers')
            ->will($this->returnValue(new ArrayCollection(array(
                $this->autoRoute1,
                $this->nonAutoRoute
            ))));

        $this->autoRouteStack->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->builderContext));

        $this->builderContext->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($this->doc));

        $this->dm->expects($this->once())
            ->method('getUnitOfWork')
            ->will($this->returnValue($this->uow));

        $this->autoRouteStack->expects($this->once())
            ->method('addRoute')
            ->with($this->autoRoute1);

        $this->arm->createOrUpdateAutoRoute($this->autoRouteStack);
    }

    public function testCreateOrUpdateAutoRouteForNew()
    {
        $testCase = $this;
        $this->dm->expects($this->once())
            ->method('getReferrers')
            ->will($this->returnValue(new ArrayCollection(array(
            ))));

        $this->autoRouteStack->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->builderContext));

        $this->builderContext->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($this->doc));

        $this->autoRouteStack->expects($this->once())
            ->method('addRoute')
            ->will($this->returnCallback(function ($route) use ($testCase) {
                $testCase->assertInstanceOf('Symfony\Cmf\Bundle\RoutingAutoBundle\Document\AutoRoute', $route);
            }));

        $this->arm->createOrUpdateAutoRoute($this->autoRouteStack);
    }
}
