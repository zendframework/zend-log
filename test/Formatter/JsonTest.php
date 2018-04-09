<?php
/**
 * @see       https://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-log/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use Zend\Log\Formatter\Json;

/**
 * @group      Zend_Log
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultFormat()
    {
        $date = new DateTime();
        $f = new Json();
        $line = $f->format(['timestamp' => $date, 'message' => 'foo', 'priority' => 42]);
        $json = json_decode($line);


        $this->assertEquals($date->format('c'), $json->timestamp);
        $this->assertEquals('foo', $json->message);
        $this->assertEquals((string)42, $json->priority);
    }


    /**
     * @dataProvider provideDateTimeFormats
     */
    public function testSetDateTimeFormat($dateTimeFormat)
    {
        $date = new DateTime();
        $f = new Json();
        $f->setDateTimeFormat($dateTimeFormat);

        $line = $f->format(['timestamp' => $date]);
        $json = json_decode($line);

        $this->assertEquals($date->format($dateTimeFormat), $json->timestamp);
    }


    public function provideDateTimeFormats()
    {
        return [
            ['r'],
            ['U'],
        ];
    }
}
