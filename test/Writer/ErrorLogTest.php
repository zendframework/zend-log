<?php
namespace ZendTest\Log\Writer;

use Zend\Log\Logger;
use Zend\Log\Writer\ErrorLog;

class ErrorLogTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $options = [
            'mode' => 3,
            'destination' => 'php://output',
        ];

        $this->writer = new ErrorLog($options);
    }


    public function testSimpleOutput()
    {
        $this->expectOutputString('%timestamp% %priorityName% (42): I would like to say %extra%');
        $event = [
            'message' => 'I would like to say',
            'priority' => 42
        ];

        $this->writer->write($event);
    }


    public function testCreationViaPluginManager()
    {
        $logger = new Logger([
            'writers' => [
                [
                    'name' => 'errorlog'
                ]
            ]
        ]);
    }
}
