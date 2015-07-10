<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Log\LogLevel;
use Zend\Log\Logger;
use Zend\Log\Psr3LoggerAdapter;

/**
 * @group Zend_Log
 * @coversDefaultClass Zend\Log\Psr3LoggerAdapter
 * @covers ::<!public>
 */
class Psr3LoggerAdapterTest extends TestCase
{
    /**
     *
     * @covers ::__construct
     * @covers ::getLogger
     */
    public function testSetLogger()
    {
        $logger = new Logger;

        $adapter = new Psr3LoggerAdapter($logger);
        $this->assertAttributeEquals($logger, 'logger', $adapter);

        $this->assertSame($logger, $adapter->getLogger($logger));
    }

    /**
     *
     * @covers ::emergency
     * @covers ::alert
     * @covers ::critical
     * @covers ::error
     * @covers ::warning
     * @covers ::notice
     * @covers ::info
     * @covers ::debug
     * @dataProvider levelSpecificMethodsToPriorityProvider
     */
    public function testPsr3LevelSpecificMethods($method, $priority)
    {
        $message = 'foo';
        $context = ['bar' => 'baz'];

        $logger = $this->getMock('Zend\Log\Logger', ['log']);
        $logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo($priority),
                $this->equalTo($message),
                $this->equalTo($context)
            );

        $adapter = new Psr3LoggerAdapter($logger);
        $adapter->{$method}($message, $context);
    }

    /**
     *
     * Data provider
     *
     * @return array
     */
    public function levelSpecificMethodsToPriorityProvider()
    {
        return [
            ['emergency', Logger::EMERG],
            ['alert', Logger::ALERT],
            ['critical', Logger::CRIT],
            ['error', Logger::ERR],
            ['warning', Logger::WARN],
            ['notice', Logger::NOTICE],
            ['info', Logger::INFO],
            ['debug', Logger::DEBUG],
        ];
    }

    /**
     * @covers ::log
     * @dataProvider logLevelsToPriorityProvider
     */
    public function testPsr3LogLevels($logLevel, $priority)
    {
        $message = 'foo';
        $context = ['bar' => 'baz'];

        $logger = $this->getMock('Zend\Log\Logger', ['log']);
        $logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo($priority),
                $this->equalTo($message),
                $this->equalTo($context)
            );

        $adapter = new Psr3LoggerAdapter($logger);
        $adapter->log($logLevel, $message, $context);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function logLevelsToPriorityProvider()
    {
        return [
            [LogLevel::EMERGENCY, Logger::EMERG],
            [LogLevel::ALERT, Logger::ALERT],
            [LogLevel::CRITICAL, Logger::CRIT],
            [LogLevel::ERROR, Logger::ERR],
            [LogLevel::WARNING, Logger::WARN],
            [LogLevel::NOTICE, Logger::NOTICE],
            [LogLevel::INFO, Logger::INFO],
            [LogLevel::DEBUG, Logger::DEBUG],
        ];
    }

    /**
     *
     * @covers ::log
     */
    public function testThrowsExceptionOnUnknownLevel()
    {
        $this->setExpectedException('Psr\Log\InvalidArgumentException');

        $logger = $this->getMock('Zend\Log\Logger');

        $adapter = new Psr3LoggerAdapter($logger);
        $adapter->log('unknown', 'foo');
    }
}
