<?php
namespace Csanquer\DebugTools\Test\Output;

use Csanquer\DebugTools\Output\AbstractOutput;
use Csanquer\DebugTools\Output\OutputInterface;

class AbstractOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Csanquer\DebugTools\Output\OutputInterface
     */
    protected $output;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->output = $this->getMockForAbstractClass('Csanquer\DebugTools\Output\AbstractOutput');
    }

    /**
     * @dataProvider getSetDefaultIndentCharProvider
     */
    public function testGetSetDefaultIndentChar($value, $expected)
    {
        $this->assertEquals(' ',$this->output->getDefaultIndentChar());
        $this->assertInstanceOf('Csanquer\DebugTools\Output\AbstractOutput', $this->output->setDefaultIndentChar($value));
        $this->assertEquals($expected, $this->output->getDefaultIndentChar());
    }
    
    public function getSetDefaultIndentCharProvider()
    {
        return array(
            array(null, ''),
            array('  ', '  '),
            array("\t", "\t"),
        );
    }
    
    /**
     * @dataProvider getSetDefaultIndentNumberProvider
     */
    public function testGetSetDefaultIndentNumber($value, $expected)
    {
        $this->assertEquals(4,$this->output->getDefaultIndentNumber());
        $this->assertInstanceOf('Csanquer\DebugTools\Output\AbstractOutput', $this->output->setDefaultIndentNumber($value));
        $this->assertEquals($expected, $this->output->getDefaultIndentNumber());
    }
    
    public function getSetDefaultIndentNumberProvider()
    {
        return array(
            array(null, 0),
            array('foobar', 0),
            array(2, 2),
        );
    }
}
