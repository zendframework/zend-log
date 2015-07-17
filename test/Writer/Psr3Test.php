<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Zend\Log\Filter\Mock as MockFilter;
use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Log\Logger;
use Zend\Log\Writer\Psr3 as Psr3Writer;

/**
 * @group      Zend_Log
 * @coversDefaultClass Zend\Log\Writer\Psr3
 * @covers ::<!public>
 */
class Psr3Test extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @covers ::__construct
     */
    public function testConstructWithPsr3Logger()
    {
        $psr3Logger = $this->getMock(LoggerInterface::class);
        $writer = new Psr3Writer($psr3Logger);
        $this->assertAttributeSame($psr3Logger, 'logger', $writer);
    }

    /**
     *
     * @covers ::__construct
     */
    public function testConstructWithOptions()
    {
        $psr3Logger = $this->getMock(LoggerInterface::class);
        $formatter = new SimpleFormatter();
        $filter    = new MockFilter();
        $writer = new Psr3Writer([
                'filters'   => $filter,
                'formatter' => $formatter,
                'logger'    => $psr3Logger,
        ]);

        $this->assertAttributeSame($psr3Logger, 'logger', $writer);
        $this->assertAttributeSame($formatter, 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }

    /**
     *
     * @covers ::__construct
     */
    public function testFallbackLoggerIsNullLogger()
    {
        $writer = new Psr3Writer;
        $this->assertAttributeInstanceOf(NullLogger::class, 'logger', $writer);
    }

    /**
     * @dataProvider priorityToLogLevelProvider
     */
    public function testWriteLogMapsLevelsProperly($priority, $logLevel)
    {
        $message = 'foo';
        $extra = ['bar' => 'baz'];

        $psr3Logger = $this->getMock(LoggerInterface::class);
        $psr3Logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo($logLevel),
                $this->equalTo($message),
                $this->equalTo($extra)
            );

        $writer = new Psr3Writer($psr3Logger);
        $logger = new Logger();
        $logger->addWriter($writer);

        $logger->log($priority, $message, $extra);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function priorityToLogLevelProvider()
    {
        return [
            [Logger::EMERG, LogLevel::EMERGENCY],
            [Logger::ALERT, LogLevel::ALERT],
            [Logger::CRIT, LogLevel::CRITICAL],
            [Logger::ERR, LogLevel::ERROR],
            [Logger::WARN, LogLevel::WARNING],
            [Logger::NOTICE, LogLevel::NOTICE],
            [Logger::INFO, LogLevel::INFO],
            [Logger::DEBUG, LogLevel::DEBUG],
        ];
    }
}
