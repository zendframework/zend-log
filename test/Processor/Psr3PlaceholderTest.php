<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Processor;

use Zend\Log\Processor\Psr3Placeholder;
use stdClass;

/**
 * @group      Zend_Log
 * @coversDefaultClass Zend\Log\Processor\Psr3Placeholder
 */
class Psr3PlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pairsProvider
     * @covers ::process
     */
    public function testReplacement($val, $expected)
    {
        $psr3Processor = new Psr3Placeholder;
        $event = $psr3Processor->process([
            'message' => '{foo}',
            'extra' => ['foo' => $val]
        ]);
        $this->assertEquals($expected, $event['message']);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function pairsProvider()
    {
        return [
            ['foo', 'foo'],
            ['3', '3'],
            [3, '3'],
            [null, ''],
            [true, '1'],
            [false, ''],
            [new stdClass, '[object stdClass]'],
            [[], '[array]'],
        ];
    }
}
