<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer\Factory;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Log\Writer\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\Log\Writer\TestAsset\InvokableObject;

class InvokableFactoryTest extends TestCase
{
    /**
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::__construct
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::createService
     */
    public function testConstructorWithoutOptions()
    {
        // Arrange
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);
        $container->getServiceLocator()->willReturn(null);

        $factory = new InvokableFactory();

        // Act
        $object = $factory->createService($container, InvokableObject::class, InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::__construct
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::createService
     */
    public function TODOtestConstructorWithOptions()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $factory = new InvokableFactory(['foo' => 'bar']);

        // Act
        $object = $factory->createService($container, InvokableObject::class, InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::__construct
     * @covers \Zend\Log\Writer\Factory\InvokableFactory::__invoke
     */
    public function testInvoke()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);
        $factory = new InvokableFactory(['foo' => 'bar']);

        // Act
        $object = $factory($container, InvokableObject::class, []);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }
}
