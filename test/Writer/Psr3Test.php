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
        $psr3Logger = $this->getMock('Psr\Log\LoggerInterface');
        $writer = new Psr3Writer($psr3Logger);
        $this->assertAttributeSame($psr3Logger, 'logger', $writer);
    }

    /**
     *
     * @covers ::__construct
     */
    public function testConstructWithOptions()
    {
        $psr3Logger = $this->getMock('Psr\Log\LoggerInterface');
        $formatter = new \Zend\Log\Formatter\Simple();
        $filter    = new \Zend\Log\Filter\Mock();
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
        $this->assertAttributeInstanceOf('Psr\Log\NullLogger', 'logger', $writer);
    }

    /**
     * @dataProvider priorityToLogLevelProvider
     */
    public function testWriteLogMapsLevelsProperly($priority, $logLevel)
    {
        $message = 'foo';
        $extra = ['bar' => 'baz'];

        $psr3Logger = $this->getMock('Psr\Log\LoggerInterface');
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
