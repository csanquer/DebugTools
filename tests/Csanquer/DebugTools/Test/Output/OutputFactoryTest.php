<?php
namespace Csanquer\DebugTools\Test\Output;

use Csanquer\DebugTools\Output\OutputFactory;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-04-09 at 10:38:58.
 */
class OutputFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OutputFactory
     */
    protected $factory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->factory = new OutputFactory();
    }

    /**
     * @dataProvider createOutputNoModeProvider
     */
    public function testCreateOutputNoMode($isCli, $supportColor, $expected)
    {
        $mockFactory = $this->getMock('Csanquer\DebugTools\Output\OutputFactory', array('isCli', 'canSupportColor'));
        
        $mockFactory->expects($this->any())
            ->method('isCli')
            ->will($this->returnValue($isCli))
            ;
        
        $mockFactory->expects($this->any())
            ->method('canSupportColor')
            ->will($this->returnValue($supportColor))
            ;
        
        $output = $mockFactory->createOutput();
        $this->assertInstanceOf($expected, $output);
    }
    
    public function createOutputNoModeProvider()
    {
        return array(
            array(true, false, 'Csanquer\DebugTools\Output\Cli'),
            array(true, true, 'Csanquer\DebugTools\Output\ColorCli'),
            array(false, false, 'Csanquer\DebugTools\Output\Html'),
            array(false, true, 'Csanquer\DebugTools\Output\Html'),
        );
    }
    
    /**
     * @dataProvider createOutputProvider
     */
    public function testCreateOutput($mode, $expected)
    {
        $output = $this->factory->createOutput($mode);
        if (empty($expected))
        {
            $this->assertNull($output); 
        }
        else
        {
            $this->assertInstanceOf($expected, $output);
        }
    }

    public function createOutputProvider()
    {
        return array(
            array(OutputFactory::MODE_CLI, 'Csanquer\DebugTools\Output\Cli'),
            array(OutputFactory::MODE_COLOR_CLI, 'Csanquer\DebugTools\Output\ColorCli'),
            array(OutputFactory::MODE_HTML, 'Csanquer\DebugTools\Output\Html'),
            array(OutputFactory::MODE_NO_FORMAT, null),
        );
    }
    
    /**
     * @covers Csanquer\DebugTools\Output\OutputFactory::getAvailableOutput
     */
    public function testGetAvailableOutput()
    {
        $this->assertEquals(array(
            OutputFactory::MODE_CLI,
            OutputFactory::MODE_COLOR_CLI,
            OutputFactory::MODE_HTML,
            OutputFactory::MODE_NO_FORMAT,
        ), $this->factory->getAvailableOutput());
    }
}