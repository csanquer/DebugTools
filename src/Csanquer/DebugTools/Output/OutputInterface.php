<?php

namespace Csanquer\DebugTools\Output;

/**
 * OutputInterface
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
interface OutputInterface
{
    /**
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
