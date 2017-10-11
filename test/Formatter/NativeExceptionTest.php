<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Formatter;

use DateTime;
use PHPUnit\Framework\TestCase;
use Zend\Log\Formatter\NativeException;

class NativeExceptionTest extends TestCase
{
    public function testFormat()
    {
        $date = new DateTime('2017-10-11T22:12:13+02:00');

        $event = [
            'timestamp'    => $date,
            'message'      => 'testmessage',
            'priority'     => 3,
            'priorityName' => 'ERR',
            'extra' => [
                'file'  => 'test.php',
                'line'  => 12,
                'trace' => [
                    [
                        'file'     => 'test.php',
                        'line'     => 12,
                        'function' => 'topTestMethod',
                        'class'    => 'Test',
                        'type'     => '::',
                        'args'     => [1]
                    ],
                    [
                        'file'     => 'test.php',
                        'line'     => 5,
                        'function' => 'mainTestMethod',
                        'class'    => 'Test',
                        'type'     => '::',
                        'args'     => [2]
                    ]
                ]
            ]
        ];

        $expected = trim("
[2017-10-11T22:12:13+02:00] ERR (3) testmessage in test.php:12
Stack trace:
#0 test.php(12): Test::topTestMethod(1)
#1 test.php(5): Test::mainTestMethod(2)
        ");

        $formatter = new NativeException();
        $output = $formatter->format($event);

        $this->assertEquals($expected, $output);
    }
}
