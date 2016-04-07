<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer;

use DateTime;
use MongoDB\Driver\Manager;
use Zend\Log\Writer\MongoDB as MongoDBWriter;

/**
 * @group      Zend_Log
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mongodb')) {
            $this->markTestSkipped('The mongodb PHP extension is not available');
        }

        $this->database = 'zf2_test';
        $this->collection = 'logs';

        $this->manager = new Manager('mongodb://localhost:27017');
    }

    public function testFormattingIsNotSupported()
    {
        $writer = new MongoDBWriter($this->manager, $this->database, $this->collection);

        $writer->setFormatter($this->getMock('Zend\Log\Formatter\FormatterInterface'));
        $this->assertAttributeEmpty('formatter', $writer);
    }

    public function testWriteWithDefaultSaveOptions()
    {
        $event = ['message'=> 'foo', 'priority' => 42];

        $writer = new MongoDBWriter($this->manager, $this->database, $this->collection);

        $writer->write($event);
    }

    public function testWriteWithCustomWriteConcern()
    {
        $event = ['message' => 'foo', 'priority' => 42];
        $writeConcern = ['journal' => false, 'wtimeout' => 100, 'wstring' => 1];

        $writer = new MongoDBWriter($this->manager, $this->database, $this->collection, $writeConcern);

        $writer->write($event);
    }

    public function testWriteConvertsDateTimeToMongoDate()
    {
        $date = new DateTime();
        $event = ['timestamp'=> $date];

        $writer = new MongoDBWriter($this->manager, $this->database, $this->collection);

        $writer->write($event);
    }
}
