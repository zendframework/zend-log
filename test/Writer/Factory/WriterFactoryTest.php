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
use Zend\Log\Writer\Factory\WriterFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\Log\Writer\TestAsset\InvokableObject;

class WriterFactoryTest extends TestCase
{
    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::setCreationOptions
     */
    public function testSetCreationOptions()
    {
        // Arrange
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);
        $container = $container->reveal();

        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $pluginManager->getServiceLocator()->willReturn($container);
        $pluginManager = $pluginManager->reveal();

        $factory = new WriterFactory();
        $factory->setCreationOptions(['foo' => 'bar']);

        // Act
        $object = $factory->createService($pluginManager, InvokableObject::class, InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals(['foo' => 'bar'], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__construct
     * @covers \Zend\Log\Writer\Factory\WriterFactory::createService
     */
    public function testCreateServiceWithoutCreationOptions()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $pluginManager->getServiceLocator()->willReturn($container);
        $pluginManager = $pluginManager->reveal();

        $factory = new WriterFactory();

        // Act
        $object = $factory->createService($pluginManager, InvokableObject::class, InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__construct
     * @covers \Zend\Log\Writer\Factory\WriterFactory::createService
     */
    public function testCreateServiceWithCreationOptions()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $pluginManager->getServiceLocator()->willReturn($container);
        $pluginManager = $pluginManager->reveal();

        $factory = new WriterFactory(['foo' => 'bar']);

        // Act
        $object = $factory->createService($pluginManager, InvokableObject::class, InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals(['foo' => 'bar'], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__construct
     * @covers \Zend\Log\Writer\Factory\WriterFactory::createService
     */
    public function testCreateServiceWithValidRequestName()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $pluginManager->getServiceLocator()->willReturn($container);
        $pluginManager = $pluginManager->reveal();

        $factory = new WriterFactory();

        // Act
        $object = $factory->createService($pluginManager, 'invalid', InvokableObject::class);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__construct
     * @covers \Zend\Log\Writer\Factory\WriterFactory::createService
     */
    public function testCreateServiceInvalidNames()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);

        $pluginManager = $this->prophesize(AbstractPluginManager::class);
        $pluginManager->getServiceLocator()->willReturn($container);
        $pluginManager = $pluginManager->reveal();

        $factory = new WriterFactory();

        // Assert
        $this->setExpectedException(
            InvalidServiceException::class,
            'WriterFactory requires that the requested name is provided'
        );

        // Act
        $object = $factory->createService($pluginManager, 'invalid', 'invalid');
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithoutOptions()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ServiceLocatorInterface::class);
        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, []);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithInvalidFilterManagerAsString()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, [
            'filter_manager' => 'my_manager',
        ]);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'filter_manager' => null,
        ], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithValidFilterManagerAsString()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('get')->with($this->equalTo('my_manager'))->willReturn(123);

        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, [
            'filter_manager' => 'my_manager',
        ]);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'filter_manager' => 123,
        ], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithoutFilterManager()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->at(0))->method('has')->with($this->equalTo('LogFilterManager'))->willReturn(true);
        $container->expects($this->at(1))->method('get')->with($this->equalTo('LogFilterManager'))->willReturn(123);

        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, []);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'filter_manager' => 123,
        ], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithInvalidFormatterManagerAsString()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, [
            'formatter_manager' => 'my_manager',
        ]);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'formatter_manager' => null,
        ], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithValidFormatterManagerAsString()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('get')->with($this->equalTo('my_manager'))->willReturn(123);

        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, [
            'formatter_manager' => 'my_manager',
        ]);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'formatter_manager' => 123,
        ], $object->options);
    }

    /**
     * @covers \Zend\Log\Writer\Factory\WriterFactory::__invoke
     */
    public function testInvokeWithoutFormatterManager()
    {
        // Arrange
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->at(0))->method('has')->with($this->equalTo('LogFilterManager'))->willReturn(true);
        $container->expects($this->at(1))->method('get')->with($this->equalTo('LogFilterManager'))->willReturn(null);
        $container->expects($this->at(2))->method('has')->with($this->equalTo('LogFormatterManager'))->willReturn(true);
        $container->expects($this->at(3))->method('get')->with($this->equalTo('LogFormatterManager'))->willReturn(123);

        $factory = new WriterFactory();

        // Act
        $object = $factory($container, InvokableObject::class, []);

        // Assert
        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals([
            'filter_manager' => null,
            'formatter_manager' => 123,
        ], $object->options);
    }
}
