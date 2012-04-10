<?php
namespace Csanquer\DebugTools\Output;

use Csanquer\DebugTools\Output\OutputInterface;
/**
 * AbstractOutput
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
abstract class AbstractOutput implements OutputInterface
{
    /**
     *
     * indentation style character
     * @var string
     */
    protected $defaultIndentChar = ' ';

    /**
     *
     * indentation number of character
     * @var int
     */
    protected $defaultIndentNumber = 4;
    
    /**
     *
     * @return string
     */
    public function getDefaultIndentChar()
    {
        return $this->defaultIndentChar;
    }
    
    /**
     *
     * @param string $char
     * 
     * @return \DebugTools\Output\OutputInterface 
     */
    public function setDefaultIndentChar($char)
    {
        $this->defaultIndentChar = (string) $char;
        return $this;
    }
    
    /**
     *
     * @return int
     */
    public function getDefaultIndentNumber()
    {
        return $this->defaultIndentNumber;
    }
    
    /**
     *
     * @param int $number
     * 
     * @return \DebugTools\Output\OutputInterface 
     */
    public function setDefaultIndentNumber($number)
    {
        $this->defaultIndentNumber = (int) $number;
        return $this;
    }    
}

