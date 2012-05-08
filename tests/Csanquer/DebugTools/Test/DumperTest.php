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
        $this->file = fopen(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'test.txt', 'r');
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
            array(array('a', 1 , 'b' => 'test', array( 0 => 'b')), 'Array', null),
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
        
        $file = isset($infos['file']) ? $infos['file']: null;
        $line = isset($infos['line']) ? $infos['line']: null;
        
        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline+1, $line);
        
        $wrap = new WrapperClass();
        $callline = __LINE__;
        $infos = $wrap->getCallInfos($this->dumper);
        
        $this->assertArrayHasKey('file', $infos);
        $this->assertArrayHasKey('line', $infos);
        
        $file = isset($infos['file']) ? $infos['file']: null;
        $line = isset($infos['line']) ? $infos['line']: null;
        
        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline+1, $line);
        
        $callline = __LINE__;
        $infos = wrapperFunction($this->dumper);
        $this->assertArrayHasKey('file', $infos);
        $this->assertArrayHasKey('line', $infos);
        
        $file = isset($infos['file']) ? $infos['file']: null;
        $line = isset($infos['line']) ? $infos['line']: null;
        
        $this->assertEquals(__FILE__, $file);
        $this->assertEquals($callline+1, $line);
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
            'call' => array (
                'file' => __FILE__,
                'line' => $callLine+1,
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
        $export = $this->dumper->print_r($var, 'a print_r');
        
        $expected = array(
            'name' => 'a print_r',
            'type' => 'print_r',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array (
                'file' => __FILE__,
                'line' => $callLine+1,
            ),
        );
        
        $this->assertEquals($expected, $export);
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
        $export = $this->dumper->var_dump($var, 'a var_dump');
        
        $expected = array(
            'name' => 'a var_dump',
            'type' => 'var_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array (
                'file' => __FILE__,
                'line' => $callLine+1,
            ),
        );
        
        $this->assertEquals($expected, $export);
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
        $export = $this->dumper->zval_dump($var, 'a zval_dump');
        
        $expected = array(
            'name' => 'a zval_dump',
            'type' => 'zval_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $expectedValue,
            'call' => array (
                'file' => __FILE__,
                'line' => $callLine+1,
            ),
        );
        
        $this->assertInternalType('array', $export);
        $this->assertArrayHasKey('name', $export);
        $this->assertArrayHasKey('type', $export);
        $this->assertArrayHasKey('composite', $export);
        $this->assertArrayHasKey('value', $export);
        $this->assertArrayHasKey('call', $export);
        
        $this->assertEquals($expected['name'], $export['name']);
        $this->assertEquals($expected['type'], $export['type']);
        $this->assertEquals($expected['composite'], $export['composite']);
        $this->assertEquals($expected['call'], $export['call']);
        $this->assertRegExp($expected['value'], $export['value']);
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
    
    /**
     * @covers Csanquer\DebugTools\Dumper::dump
     * @todo   Implement testDump().
     */
    public function testDump()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
        
    /**
     * @covers Csanquer\DebugTools\Dumper::backtrace
     * @dataProvider backtraceProvider
     */
    public function testBacktrace($maxChar, $expectedMaxChar)
    {
        $callLine = __LINE__;
        $export = $this->dumper->backtrace(array('max_char' => $maxChar));
        
        $expected = array(
            'name' => 'backtrace',
            'type' => 'backtrace',
            'composite' => true,
            'max_char' => $expectedMaxChar,
            'call' => array (
                'file' => __FILE__,
                'line' => $callLine+1,
            ),
        );
        
        $this->assertInternalType('array', $export);
        $this->assertArrayHasKey('name', $export);
        $this->assertArrayHasKey('type', $export);
        $this->assertArrayHasKey('composite', $export);
        $this->assertArrayHasKey('max_char', $export);
        $this->assertArrayHasKey('value', $export);
        $this->assertArrayHasKey('call', $export);
        
        $this->assertEquals($expected['name'], $export['name']);
        $this->assertEquals($expected['type'], $export['type']);
        $this->assertEquals($expected['composite'], $export['composite']);
        $this->assertEquals($expected['call'], $export['call']);
        $this->assertEquals($expected['max_char'], $export['max_char']);
        $this->assertInternalType('array', $export['value']);
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