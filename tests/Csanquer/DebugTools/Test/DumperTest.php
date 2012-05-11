<?php

namespace Csanquer\DebugTools\Test;

use Csanquer\DebugTools\Output\OutputInterface;
use Csanquer\DebugTools\Output\OutputFactory;
use Csanquer\DebugTools\Debug;
use Csanquer\DebugTools\Dumper;

class WithoutToString
{
    
}

class WithToString
{

    public function __toString()
    {
        return 'this class has __toString method';
    }

}

class WrapperClass
{

    public function getCallInfos(Dumper $debug)
    {
        return $debug->getCallInfos(__FUNCTION__, __CLASS__);
    }

}

function wrapperFunction(Dumper $debug)
{
    return $debug->getCallInfos(__FUNCTION__);
}

class TestDump
{

    protected static $f = 'static';
    private $a = true;
    protected $b = 1;
    public $c = 'hello';
    private $d = array(
        'e' => 5
    );

}

class DumperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Csanquer\DebugTools\Dumper
     */
    protected $debug;

    /**
     * @var resource
     */
    protected $file;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->dumper = new Dumper();
        $this->file = fopen(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'test.txt', 'r');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        fclose($this->file);
    }

    /**
     * @dataProvider asStringProvider
     * @covers Csanquer\DebugTools\Dumper::asString
     */
    public function testAsString($value, $expectedEquals, $expectedNotEquals)
    {
        $result = $this->dumper->asString($value);
        $this->assertInternalType('string', $result);
        $this->assertEquals($expectedEquals, $result);
        if (is_string($expectedNotEquals))
        {
            $this->assertNotEquals($expectedNotEquals, $result);
        }
    }

    public function asStringProvider()
    {
        return array(
            array(null, 'NULL', 'null'),
            array(5, '5', null),
            array(3.02, '3.02', null),
            array(false, 'FALSE', 'false'),
            array(true, 'TRUE', 'true'),
            array('a string', 'a string', 'A string'),
            array(new WithoutToString(), 'Object Csanquer\DebugTools\Test\WithoutToString', null),
            array(new WithToString(), 'this class has __toString method', 'Object Csanquer\DebugTools\Test\WithToString'),
            array(array('a', 1, 'b' => 'test', array(0 => 'b')), 'Array', null),
        );
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::asString
     */
    public function testAsStringFile()
    {
        $result = $this->dumper->asString($this->file);
        $this->assertInternalType('string', $result);
        $this->assertEquals('stream', $result);
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::getCallInfos
     */
    public function testGetCallInfos()
    {
        $callline = __LINE__;
        $infos = $this->dumper->getCallInfos();
        $this->assertArrayHasKey('file', $infos);
        $this->assertArrayHasKey('line', $infos);

        $file = isset($infos['file']) ? $infos['file'] : null;
        $line = isset($infos['line']) ? $infos['line'] : null;

        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline + 1, $line);

        $wrap = new WrapperClass();
        $callline = __LINE__;
        $infos = $wrap->getCallInfos($this->dumper);

        $this->assertArrayHasKey('file', $infos);
        $this->assertArrayHasKey('line', $infos);

        $file = isset($infos['file']) ? $infos['file'] : null;
        $line = isset($infos['line']) ? $infos['line'] : null;

        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline + 1, $line);

        $callline = __LINE__;
        $infos = wrapperFunction($this->dumper);
        $this->assertArrayHasKey('file', $infos);
        $this->assertArrayHasKey('line', $infos);

        $file = isset($infos['file']) ? $infos['file'] : null;
        $line = isset($infos['line']) ? $infos['line'] : null;

        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline + 1, $line);
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::var_export
     * @dataProvider var_exportProvider
     */
    public function testVar_export($var, $expectedValue)
    {
        $callLine = __LINE__;
        $export = $this->dumper->var_export($var, 'a var export');

        $expected = array(
            'name' => 'a var export',
            'type' => 'var_export',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertEquals($expected, $export);
    }

    public function var_exportProvider()
    {
        return array(
            array(
                1,
                '1',
            ),
            array(
                3.2,
                '3.2',
            ),
            array(
                'hello',
                '\'hello\'',
            ),
            array(
                null,
                'NULL',
            ),
            array(
                true,
                'true',
            ),
            array(
                array(2, 'a' => 'b', 'c', 'd' => 3),
                "array (\n  0 => 2,\n  'a' => 'b',\n  1 => 'c',\n  'd' => 3,\n)",
            ),
        );
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::print_r
     * @dataProvider print_rProvider
     */
    public function testPrint_r($var, $expectedValue)
    {
        $callLine = __LINE__;
        $dump = $this->dumper->print_r($var, 'a print_r');

        $expected = array(
            'name' => 'a print_r',
            'type' => 'print_r',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertEquals($expected, $dump);
    }

    public function print_rProvider()
    {
        return array(
            array(
                1,
                '1',
            ),
            array(
                3.2,
                '3.2',
            ),
            array(
                'hello',
                'hello',
            ),
            array(
                null,
                '',
            ),
            array(
                true,
                '1',
            ),
            array(
                false,
                '',
            ),
            array(
                array(2, 'a' => 'b', 'c', 'd' => 3),
                "Array\n(\n    [0] => 2\n    [a] => b\n    [1] => c\n    [d] => 3\n)\n",
            ),
        );
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::var_dump
     * @dataProvider var_dumpProvider
     */
    public function testVar_dump($var, $expectedValue)
    {
        $callLine = __LINE__;
        $dump = $this->dumper->var_dump($var, 'a var_dump');

        $expected = array(
            'name' => 'a var_dump',
            'type' => 'var_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertEquals($expected, $dump);
    }

    public function var_dumpProvider()
    {
        return array(
            array(
                1,
                "int(1)\n",
            ),
            array(
                3.2,
                "float(3.2)\n",
            ),
            array(
                'hello',
                "string(5) \"hello\"\n",
            ),
            array(
                null,
                "NULL\n",
            ),
            array(
                true,
                "bool(true)\n",
            ),
            array(
                false,
                "bool(false)\n",
            ),
            array(
                array(2, 'a' => 'b', 'c', 'd' => 3),
                "array(4) {\n  [0] => int(2)\n  [\"a\"] => string(1) \"b\"\n  [1] => string(1) \"c\"\n  [\"d\"] => int(3)\n}\n",
            ),
        );
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::zval_dump
     * @dataProvider zval_dumpProvider
     */
    public function testZval_dump($var, $expectedValue)
    {
        $callLine = __LINE__;
        $dump = $this->dumper->zval_dump($var, 'a zval_dump');

        $expected = array(
            'name' => 'a zval_dump',
            'type' => 'zval_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertInternalType('array', $dump);
        $this->assertArrayHasKey('name', $dump);
        $this->assertArrayHasKey('type', $dump);
        $this->assertArrayHasKey('composite', $dump);
        $this->assertArrayHasKey('value', $dump);
        $this->assertArrayHasKey('call', $dump);

        $this->assertEquals($expected['name'], $dump['name']);
        $this->assertEquals($expected['type'], $dump['type']);
        $this->assertEquals($expected['composite'], $dump['composite']);
        $this->assertEquals($expected['call'], $dump['call']);
        $this->assertRegExp($expected['value'], $dump['value']);
    }

    public function zval_dumpProvider()
    {
        return array(
            array(
                1,
                '/long\(1\)\srefcount\(\d+\)\n/',
            ),
            array(
                3.2,
                '/double\(3\.2\)\srefcount\(\d+\)\n/',
            ),
            array(
                'hello',
                '/string\(5\)\s"hello"\srefcount\(\d+\)\n/',
            ),
            array(
                null,
                '/NULL\srefcount\(\d+\)\n/',
            ),
            array(
                true,
                '/bool\(true\)\srefcount\(\d+\)\n/',
            ),
            array(
                false,
                '/bool\(false\)\srefcount\(\d+\)\n/',
            ),
            array(
                array(2, 'a' => 'b', 'c', 'd' => 3),
                '/array\(4\)\srefcount\(\d+\)\{\n\s+\[0\]\s=>\slong\(2\)\srefcount\(\d+\)\n\s+\["a"\]\s=>\sstring\(1\)\s"b"\srefcount\(\d+\)\n\s+\[1\]\s=>\sstring\(1\)\s"c"\srefcount\(\d+\)\n\s+\["d"\]\s=>\slong\(3\)\srefcount\(\d+\)\n}\n/'
            ),
        );
    }

    public function testDumpResource()
    {
        $callLine = __LINE__;
        $dump = $this->dumper->dump($this->file, 'a custom resource dump', null, array('max_char' => null));

        $expected = array(
            'type' => 'resource',
            'composite' => false,
            'value' => '163',
            'resource_type' => 'stream',
            'name' => 'a custom resource dump',
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertEquals($expected, $dump);
    }

    public function testDumpException()
    {
        $line = __LINE__ +1;
        $exception = new \ErrorException('an error occured', 101, 1, __FILE__, __LINE__, new \Exception('an exception'));

        $callLine = __LINE__;
        $dump = $this->dumper->dump($exception, 'an exception', null, array('max_char' => null));

        $this->assertInternalType('array', $dump);
        $this->assertArrayHasKey('name', $dump);
        $this->assertArrayHasKey('type', $dump);
        $this->assertArrayHasKey('composite', $dump);
        $this->assertArrayHasKey('class', $dump);
        $this->assertArrayHasKey('properties', $dump);
        $this->assertArrayHasKey('call', $dump);

        $this->assertEquals('an exception', $dump['name']);
        $this->assertEquals('exception', $dump['type']);
        $this->assertTrue($dump['composite']);
        $this->assertEquals(array(
            'file' => __FILE__,
            'line' => $callLine + 1,
                ), $dump['call']);

        $this->assertEquals('ErrorException', $dump['class']);

        $props = $dump['properties'];

        $this->assertInternalType('array', $props);
        $this->assertCount(7, $props);

        $this->assertEquals(array(
            'type' => 'property',
            'composite' => false,
            'access' => 'protected',
            'static' => false,
            'value' =>
            array(
                'type' => 'string',
                'value' => 'an error occured',
                'length' => 16,
                'max_length' => NULL,
                'composite' => false,
                )), $props['message']);

        $this->assertEquals(array(
            'type' => 'property',
            'composite' => false,
            'access' => 'protected',
            'static' => false,
            'value' =>
            array(
                'type' => 'int',
                'value' => 101,
                'composite' => false,
                )), $props['code']);

        $this->assertEquals(array(
            'type' => 'property',
            'composite' => false,
            'access' => 'protected',
            'static' => false,
            'value' =>
            array(
                'type' => 'string',
                'value' => __FILE__,
                'length' => 79,
                'max_length' => NULL,
                'composite' => false,
                )), $props['file']);

        $this->assertEquals(array(
            'type' => 'property',
            'composite' => false,
            'access' => 'protected',
            'static' => false,
            'value' =>
            array(
                'type' => 'int',
                'value' => $line,
                'composite' => false,
                )), $props['line']);

        $this->assertEquals(array(
            'type' => 'property',
            'composite' => false,
            'access' => 'protected',
            'static' => false,
            'value' =>
            array(
                'type' => 'int',
                'value' => 1,
                'composite' => false,
                )), $props['severity']);
        
        //trace
        $this->assertArrayHasKey('trace', $props);
        $this->assertInternalType('array', $props['trace']);
        $this->assertNotEmpty($props['trace']);
        
        // previous exception
        $this->assertArrayHasKey('previous', $props);
        $this->assertInternalType('array', $props['previous']);
        $this->assertArrayHasKey('type', $props['previous']);
        $this->assertArrayHasKey('composite', $props['previous']);
        $this->assertArrayHasKey('class', $props['previous']);
        $this->assertArrayHasKey('properties', $props['previous']);

        $this->assertEquals('exception', $props['previous']['type']);
        $this->assertTrue($props['previous']['composite']);
        $this->assertEquals('Exception', $props['previous']['class']);
        $this->assertCount(5, $props['previous']['properties']);
        
        // previous exception trace
        $this->assertArrayNotHasKey('trace', $props['previous']['properties']);
    }

    /**
     * @dataProvider dumpProvider
     */
    public function testDump($var, $maxDepth, $maxChar, $expected)
    {
        $callLine = __LINE__;
        $dump = $this->dumper->dump($var, 'a custom dump', $maxDepth, array('max_char' => $maxChar));

        $expected = array_merge(
                $expected, array(
            'name' => 'a custom dump',
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
                )
        );

        $this->assertEquals($expected, $dump);
    }

    public function dumpProvider()
    {
        $longString = 'Nullam sit amet libero orci, id bibendum sem. Nunc hendrerit elit ac mauris rhoncus vitae sollicitudin leo pharetra. Nunc imperdiet ultrices purus volutpat elementum. In hendrerit tristique augue, ultrices posuere eros pellentesque in. Sed velit dolor, ultrices eu consectetur vel, lacinia vitae nulla. Aenean purus est, venenatis quis pulvinar vitae, mollis in velit. Pellentesque laoreet, risus vel adipiscing condimentum, justo neque tincidunt nulla, at mollis lacus ipsum vitae ligula? Cras rutrum vestibulum augue in dignissim. Donec neque ligula, rutrum quis faucibus posuere, pharetra a arcu.';

        return array(
            array(
                null,
                null,
                null,
                array(
                    'type' => 'NULL',
                    'composite' => false,
                    'value' => null,
                )
            ),
            array(
                true,
                null,
                null,
                array(
                    'type' => 'bool',
                    'composite' => false,
                    'value' => true,
                )
            ),
            array(
                false,
                null,
                null,
                array(
                    'type' => 'bool',
                    'composite' => false,
                    'value' => false,
                )
            ),
            array(
                2,
                null,
                null,
                array(
                    'type' => 'int',
                    'composite' => false,
                    'value' => 2,
                )
            ),
            array(
                2.1,
                null,
                null,
                array(
                    'type' => 'float',
                    'composite' => false,
                    'value' => 2.1,
                )
            ),
            array(
                'hello',
                null,
                null,
                array(
                    'type' => 'string',
                    'composite' => false,
                    'value' => 'hello',
                    'length' => 5,
                    'max_length' => null,
                )
            ),
            array(
                $longString,
                null,
                20,
                array(
                    'type' => 'string',
                    'composite' => false,
                    'value' => 'Nullam sit amet libe',
                    'length' => 599,
                    'max_length' => 20,
                )
            ),
            array(
                new TestDump(),
                null,
                null,
                array(
                    'type' => 'object',
                    'composite' => true,
                    'class' => 'Csanquer\DebugTools\Test\TestDump',
                    'properties' => array(
                        'f' =>
                        array(
                            'type' => 'property',
                            'composite' => false,
                            'access' => 'protected',
                            'static' => true,
                            'value' =>
                            array(
                                'type' => 'string',
                                'value' => 'static',
                                'length' => 6,
                                'max_length' => NULL,
                                'composite' => false,
                            ),
                        ),
                        'a' =>
                        array(
                            'type' => 'property',
                            'composite' => false,
                            'access' => 'private',
                            'static' => false,
                            'value' =>
                            array(
                                'type' => 'bool',
                                'value' => true,
                                'composite' => false,
                            ),
                        ),
                        'b' =>
                        array(
                            'type' => 'property',
                            'composite' => false,
                            'access' => 'protected',
                            'static' => false,
                            'value' =>
                            array(
                                'type' => 'int',
                                'value' => 1,
                                'composite' => false,
                            ),
                        ),
                        'c' =>
                        array(
                            'type' => 'property',
                            'composite' => false,
                            'access' => 'public',
                            'static' => false,
                            'value' =>
                            array(
                                'type' => 'string',
                                'value' => 'hello',
                                'length' => 5,
                                'max_length' => NULL,
                                'composite' => false,
                            ),
                        ),
                        'd' =>
                        array(
                            'type' => 'property',
                            'composite' => false,
                            'access' => 'private',
                            'static' => false,
                            'value' =>
                            array(
                                'type' => 'array',
                                'composite' => true,
                                'value' =>
                                array(
                                    'e' =>
                                    array(
                                        'type' => 'int',
                                        'value' => 5,
                                        'composite' => false,
                                    ),
                                ),
                                'length' => 1,
                            ),
                        ),
                    ),
                )
            ),
            array(
                array(
                    'a' => 1,
                    'b' => 'hello',
                    array(
                        'c' => array(
                            array(
                                2 => array(
                                    'd' => null,
                                    false
                                ),
                            ),
                            'e' => true
                        ),
                    ),
                ),
                null,
                null,
                array(
                    'type' => 'array',
                    'composite' => true,
                    'length' => 3,
                    'value' => array(
                        'a' =>
                        array(
                            'type' => 'int',
                            'value' => 1,
                            'composite' => false,
                        ),
                        'b' =>
                        array(
                            'type' => 'string',
                            'value' => 'hello',
                            'length' => 5,
                            'max_length' => null,
                            'composite' => false,
                        ),
                        0 => array(
                            'type' => 'array',
                            'composite' => true,
                            'value' =>
                            array(
                                'c' =>
                                array(
                                    'type' => 'array',
                                    'composite' => true,
                                    'value' =>
                                    array(
                                        0 =>
                                        array(
                                            'type' => 'array',
                                            'composite' => true,
                                            'value' =>
                                            array(
                                                2 =>
                                                array(
                                                    'type' => 'array',
                                                    'composite' => true,
                                                    'value' => '...',
                                                    'length' => 2,
                                                ),
                                            ),
                                            'length' => 1,
                                        ),
                                        'e' =>
                                        array(
                                            'type' => 'bool',
                                            'value' => true,
                                            'composite' => false,
                                        ),
                                    ),
                                    'length' => 2,
                                ),
                            ),
                            'length' => 1,
                        ),
                    ),
                )
            ),
            array(
                array(
                    'a' => 1,
                    'b' => 'hello',
                    array(
                        'c' => array(
                            array(
                                2 => array(
                                    'd' => null,
                                    false
                                ),
                            ),
                            'e' => true
                        ),
                    ),
                ),
                2,
                null,
                array(
                    'type' => 'array',
                    'composite' => true,
                    'length' => 3,
                    'value' => array(
                        'a' =>
                        array(
                            'type' => 'int',
                            'value' => 1,
                            'composite' => false,
                        ),
                        'b' =>
                        array(
                            'type' => 'string',
                            'value' => 'hello',
                            'length' => 5,
                            'max_length' => null,
                            'composite' => false,
                        ),
                        0 => array(
                            'type' => 'array',
                            'composite' => true,
                            'value' =>
                            array(
                                'c' =>
                                array(
                                    'type' => 'array',
                                    'composite' => true,
                                    'value' => '...',
                                    'length' => 2,
                                ),
                            ),
                            'length' => 1,
                        ),
                    ),
                )
            ),
        );
    }

    /**
     * @covers Csanquer\DebugTools\Dumper::backtrace
     * @dataProvider backtraceProvider
     */
    public function testBacktrace($maxChar, $expectedMaxChar)
    {
        $callLine = __LINE__;
        $trace = $this->dumper->backtrace(array('max_char' => $maxChar));

        $expected = array(
            'name' => 'backtrace',
            'type' => 'backtrace',
            'composite' => true,
            'max_char' => $expectedMaxChar,
            'call' => array(
                'file' => __FILE__,
                'line' => $callLine + 1,
            ),
        );

        $this->assertInternalType('array', $trace);
        $this->assertArrayHasKey('name', $trace);
        $this->assertArrayHasKey('type', $trace);
        $this->assertArrayHasKey('composite', $trace);
        $this->assertArrayHasKey('max_char', $trace);
        $this->assertArrayHasKey('value', $trace);
        $this->assertArrayHasKey('call', $trace);

        $this->assertEquals($expected['name'], $trace['name']);
        $this->assertEquals($expected['type'], $trace['type']);
        $this->assertEquals($expected['composite'], $trace['composite']);
        $this->assertEquals($expected['call'], $trace['call']);
        $this->assertEquals($expected['max_char'], $trace['max_char']);
        $this->assertInternalType('array', $trace['value']);
    }

    public function backtraceProvider()
    {
        return array(
            array(
                null,
                180,
            ),
            array(
                100,
                100,
            ),
        );
    }

}