<?php

namespace Csanquer\DebugTools\Output;

/**
 * OutputInterface
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 * @codeCoverageIgnore
 */
interface OutputInterface
{
    /**
     * get a string representation of dump information array for this output
     * 
     * @param array $dump
     * 
     * @return string 
     */
    public function format($dump);
    
    /**
     *
     * @return string
     */
    public function getDefaultIndentChar();
    
    /**
     *
     * @param string $char
     * 
     * @return \DebugTools\Output\OutputInterface
     */
    public function setDefaultIndentChar($char);
    
    /**
     *
     * @return int
     */
    public function getDefaultIndentNumber();
    
    /**
     *
     * @param int $number
     * 
     * @return \DebugTools\Output\OutputInterface
     */
    public function setDefaultIndentNumber($number);
    
    
}
