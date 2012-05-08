<?php

namespace Csanquer\DebugTools;

use Csanquer\DebugTools\Output\OutputInterface;
use Csanquer\DebugTools\Output\OutputFactory;
use Csanquer\DebugTools\Dumper;

/**
 * Debugging tool class
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Debug
{
    protected $dumper;
    
    /**
     * security : max recursion when iterate over arrays and objects
     * @var int
     */
    protected $defaultMaxDepth = 4;
    
    /**
     *
     * @var int
     */
    protected $defaultMode = OutputFactory::MODE_CLI; 
    
    /**
     *
     * @var \DebugTools\Output\OutputInterface
     */
    protected $defaultOutput;
    
    public function __construct()
    {
        $this->dumper = new Dumper();
    }

    
    /**
     *
     * @return int
     */
    public function getDefaultMaxDepth()
    {
        return $this->defaultMaxDepth;
    }
    
    /**
     *
     * @param int $defaultMaxDepth
     * @return \DebugTools\Debug 
     */
    public function setDefaultMaxDepth($defaultMaxDepth)
    {
        $this->defaultMaxDepth = (int) $defaultMaxDepth;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getDefaultMode()
    {
        return $this->defaultMode;
    }
    
    /**
     *
     * @param string $mode
     * @return \DebugTools\Debug 
     */
    public function setDefaultMode($mode)
    {
        if (in_array($mode, OutputFactory::getAvailableOutput()))
        {
            $this->defaultMode = $mode;
            $this->defaultOutput = $this->createOutput($this->getDefaultMode());
        }
        return $this;
    }
    
    /**
     *
     * @param string $mode
     * @return \DebugTools\Output\OutputInterface 
     */
    protected function createOutput($mode)
    {
        $factory = new OutputFactory();
        return $factory->createOutput($mode);
    }
    
    /**
     *
     * @return \DebugTools\Output\OutputInterface 
     */
    protected function getDefaultOutput()
    {
        if (empty($this->defaultOutput))
        {
            $this->defaultOutput = $this->createOutput($this->getDefaultMode());
        }
        return $this->defaultOutput;
    }
    
    /**
     *
     * @param array $dump
     * @param string|bool $mode
     * @param bool $return
     * 
     * @return string|array|null 
     */
    protected function format($dump, $mode = true, $return = true)
    {
        if (empty($mode) || $mode === OutputFactory::MODE_NO_FORMAT)
        {
            return $dump;
        }
        
        $output = null;
        if ($mode === true || $mode == $this->getDefaultMode())
        {
            $output = $this->getDefaultOutput();
        }
        elseif (in_array($mode, OutputFactory::getAvailableOutput()))
        {
            $output = $this->createOutput($mode);
        }
        
        if ($output instanceof OutputInterface)
        {
            if ($return)
            {
                return $output->format($dump);
            }
            else
            {
                echo $output->format($dump);
                return;
            }
        }
        return $dump;
    }
    
    /**
     * var_export to HTML if PHP is running in cli mode
     * return print_r result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string|array 
     */
    public function var_export($var, $name = null, $return = false, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->var_export($var, $name, $options), $mode, $return);
    }
    
    /**
     * print_r to HTML if PHP is running in cli mode
     * return print_r result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string|array 
     */
    public function print_r($var, $name = null, $return = false, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->print_r($var, $name, $options), $mode, $return);
    }

    /**
     * var_dump to HTML if PHP is running in cli mode
     * return var_dump result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string|array 
     */
    public function var_dump($var, $name = null, $return = false, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->var_dump($var, $name, $options), $mode, $return);
    }

    /**
     * debug_zval_dump to HTML if PHP is running in HTML mode
     * return debug_zval_dump result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string|array 
     */
    public function zval_dump($var, $name = null, $return = false, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->zval_dump($var, $name, $options), $mode, $return);
    }

    /**
     * return the backtrace as an HTML array
     *
     * @access public
     *
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - int max_char = 180, max character to show if the variable is a string <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string|array 
     */
    public function backtrace($return = false, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->backtrace($options), $mode, $return);
    }    
    
    /**
     *
     * @param mixed $var
     * @param string $name default = null, variable name to display
     * @param int $maxDepth default = 4
     * @param bool $return default = false, if true return, else echo the result
     * @param bool $mode default = true, if true format the dump , else return dump array infos
     * @param array $options default = array(), available options : <br/>
     *   - int max_char = 180, max character to show if the variable is a string <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     * 
     * @return string|array 
     */
    public function dump($var, $name = null, $maxDepth = 4, $return = true, $mode = true, array $options = array())
    {
        if (empty($options['function']))
        {
             $options['function'] = __FUNCTION__;
        }
        
        if (empty($options['class']))
        {
             $options['class'] = __CLASS__;
        }
        
        return $this->format($this->dumper->dump($var, $name, $maxDepth, $options), $mode, $return);
    }

    /**
     * Simple Conversion from variable to String representation
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @return string
     */
    public function asString($var)
    {
        return $this->dumper->asString($var);
    }
}
