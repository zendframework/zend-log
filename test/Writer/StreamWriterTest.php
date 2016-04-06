<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Writer;

use org\bovigo\vfs\vfsStream;
use Zend\Log\Filter\Mock;
use Zend\Log\Writer\Stream as StreamWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;

/**
 * @group      Zend_Log
 */
class StreamWriterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup('zend-log');
    }

    public function testConstructorThrowsWhenResourceIsNotStream()
    {
        $resource = xml_parser_create();
        try {
            new StreamWriter($resource);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Log\Exception\InvalidArgumentException', $e);
            $this->assertRegExp('/not a stream/i', $e->getMessage());
        }
        xml_parser_free($resource);
    }

    public function testConstructorWithValidStream()
    {
        $stream = fopen('php://memory', 'w+');
        new StreamWriter($stream);
    }

    public function testConstructorWithValidUrl()
    {
        new StreamWriter('php://memory');
    }

    public function testConstructorThrowsWhenModeSpecifiedForExistingStream()
    {
        $stream = fopen('php://memory', 'w+');
        $this->setExpectedException('Zend\Log\Exception\InvalidArgumentException', 'existing stream');
        new StreamWriter($stream, 'w+');
    }

    public function testConstructorThrowsWhenStreamCannotBeOpened()
    {
        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'cannot be opened');
        new StreamWriter('');
    }

    public function testWrite()
    {
        $stream = fopen('php://memory', 'w+');
        $fields = ['message' => 'message-to-log'];

        $writer = new StreamWriter($stream);
        $writer->write($fields);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($fields['message'], $contents);
    }

    public function testWriteThrowsWhenStreamWriteFails()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        fclose($stream);

        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'Unable to write');
        $writer->write(['message' => 'foo']);
    }

    public function testShutdownClosesStreamResource()
    {
        $writer = new StreamWriter('php://memory', 'w+');
        $writer->write(['message' => 'this write should succeed']);

        $writer->shutdown();

        $this->setExpectedException('Zend\Log\Exception\RuntimeException', 'Unable to write');
        $writer->write(['message' => 'this write should fail']);
    }

    public function testSettingNewFormatter()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        $expected = 'foo';

        $formatter = new SimpleFormatter($expected);
        $writer->setFormatter($formatter);

        $writer->write(['bar'=>'baz']);
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($expected, $contents);
    }

    public function testAllowSpecifyingLogSeparator()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new StreamWriter($stream);
        $writer->setLogSeparator('::');

        $fields = ['message' => 'message1'];
        $writer->write($fields);
        $fields['message'] = 'message2';
        $writer->write($fields);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertRegexp('/message1.*?::.*?message2/', $contents);
        $this->assertNotContains(PHP_EOL, $contents);
    }

    public function testAllowsSpecifyingLogSeparatorAsConstructorArgument()
    {
        $writer = new StreamWriter('php://memory', 'w+', '::');
        $this->assertEquals('::', $writer->getLogSeparator());
    }

    public function testAllowsSpecifyingLogSeparatorWithinArrayPassedToConstructor()
    {
        $options = [
            'stream'        => 'php://memory',
            'mode'          => 'w+',
            'log_separator' => '::',
        ];
        $writer = new StreamWriter($options);
        $this->assertEquals('::', $writer->getLogSeparator());
    }

    public function testConstructWithOptions()
    {
        $formatter = new SimpleFormatter();
        $filter    = new Mock();
        $writer = new StreamWriter([
                'filters'   => $filter,
                'formatter' => $formatter,
                'stream'        => 'php://memory',
                'mode'          => 'w+',
                'log_separator' => '::',

        ]);
        $this->assertEquals('::', $writer->getLogSeparator());
        $this->assertAttributeEquals($formatter, 'formatter', $writer);

        $filters = self::readAttribute($writer, 'filters');
        $this->assertCount(1, $filters);
        $this->assertEquals($filter, $filters[0]);
    }

    public function testDefaultFormatter()
    {
        $writer = new StreamWriter('php://memory');
        $this->assertAttributeInstanceOf('Zend\Log\Formatter\Simple', 'formatter', $writer);
    }

    public function testCanSpecifyFilePermsViaChmodOption()
    {
        $filter    = new Mock();
        $formatter = new SimpleFormatter();
        $file      = $this->root->url() . '/foo';
        $writer = new StreamWriter([
                'filters'       => $filter,
                'formatter'     => $formatter,
                'stream'        => $file,
                'mode'          => 'w+',
                'chmod'         => 0664,
                'log_separator' => '::',
        ]);

        $this->assertEquals(0664, $this->root->getChild('foo')->getPermissions());
    }

    public function testFilePermsDoNotThrowErrors()
    {
        // Make the chmod() override emit a warning.
        $GLOBALS['chmod_throw_error'] = true;

        $filter    = new Mock();
        $formatter = new SimpleFormatter();
        $file      = $this->root->url() . '/foo';
        $writer = new StreamWriter([
            'filters'       => $filter,
            'formatter'     => $formatter,
            'stream'        => $file,
            'mode'          => 'w+',
            'chmod'         => 0777,
        ]);

        $this->assertEquals(0666, $this->root->getChild('foo')->getPermissions());
    }

    public function testCanSpecifyFilePermsViaConstructorArgument()
    {
        $file = $this->root->url() . '/foo';
        new StreamWriter($file, null, null, 0755);
        $this->assertEquals(0755, $this->root->getChild('foo')->getPermissions());
    }
}

namespace Zend\Log\Writer;

/**
 * chmod() override in for tests to emulate a warning.
 *
 * @param string $filename
 * @param int $mode
 * @return bool
 * @throws \ErrorException  if $GLOBALS['chmod_throw_error'] is truthy.
 */
function chmod($filename, $mode) {
    if (!empty($GLOBALS['chmod_throw_error'])) {
        trigger_error('some_error', E_WARNING);
        return false;
    }
    return \chmod($filename, $mode);
}
