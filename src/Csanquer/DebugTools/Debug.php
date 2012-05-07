<?php

namespace Csanquer\DebugTools;

use Csanquer\DebugTools\Output\OutputInterface;
use Csanquer\DebugTools\Output\OutputFactory;

/**
 * Debugging tool class
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Debug
{
    const PHP_NO_ACCESS = 'php_no_access';
    
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
        $dump = array(
            'name' => $name,
            'type' => 'var_export', 
            'composite' => is_array($var) || is_object($var), 
            'value' => var_export($var, true),
            'call' => $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
        var_dump($dump);
        return $this->format($dump, $mode, $return);
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
        $dump = array(
            'name' => $name,
            'type' => 'print_r', 
            'composite' => is_array($var) || is_object($var), 
            'value' => print_r($var, true),
            'call' => $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
        
        return $this->format($dump, $mode, $return);
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
        ob_start();
        var_dump($var);
        $value = ob_get_clean();
        $value = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $value);

        $xdebugEnabled = extension_loaded('xdebug');
        
        $dump = array(
            'name' => $name,
            'type' => 'var_dump', 
            'composite' => is_array($var) || is_object($var), 
            'value' => $value,
            'call' => $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
        
        return $this->format($dump, $mode, $return);
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
        ob_start();
        debug_zval_dump($var);
        $value = ob_get_clean();
        $value = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $value);
        
        $dump = array(
            'name' => $name,
            'type' => 'zval_dump', 
            'composite' => is_array($var) || is_object($var), 
            'value' => $value,
            'call' => $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
        
        return $this->format($dump, $mode, $return);
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
        $dump = array(
            'name' => 'backtrace',
            'type' => 'backtrace',
            'composite' => true,
            'max_char' => isset($options['max_char']) ? (int) $options['max_char'] : 180, 
            'value' => debug_backtrace(),
            'call' => $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
        
        return $this->format($dump, $mode, $return);
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
        $dump = $this->doDump(
                $var, 
                $maxDepth, 
                isset($options['max_char']) ? (int) $options['max_char'] : null, 
                isset($options['dump_content']) ? (bool) $options['dump_content'] : true, 
                isset($options['with_trace']) ? (bool) $options['with_trace'] : true, 
                0
        );
        
        $dump['name'] = $name;
        $dump['call'] = $this->getCallInfos(empty($options['function'])? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class']);
        
        return $this->format($dump, $mode, $return);
    }
    
    /**
     *
     * @param mixed $var
     * @param int $maxDepth default = 4
     * @param int $maxCharacter = null max character to show if the variable is a string
     * @param bool $dumpContent default = true, if true dump the content of an array or a object
     * @param bool $displayTrace default = true, if true and the dumped var is an exception it displays the Exception backtrace
     * @param int $depth default = 0, first call DO NOT set this parameter !!
     * 
     * @return array
     */
    protected function doDump($var, $maxDepth = 4, $maxCharacter = null, $dumpContent = true, $displayTrace = true, $depth = 0)
    {
        $dump = array();

        // scalar types
        if (is_scalar($var))
        {
            $dump = $this->doDumpScalar($var, $maxCharacter);
        }
        // special types
        elseif (is_null($var)) 
        {
            $dump = array(
                'type' => 'NULL', 
                'composite' => false,
                'value' => null,
            );
        }
        elseif (is_resource($var))
        {
            $matches = array();
            $resType = get_resource_type($var);
            preg_match('/\d+/', (string) $var, $matches);
            $dump = array(
                'type' => 'Resource', 
                'composite' => false,
                'value' => (!empty($matches[0]) ? $matches[0] : ''), 
                'resource_type' => ((empty($resType) || $resType == 'Unknown') ? 'Unknown' : $resType)
            );
        }
        // composite types
        // array
        elseif (is_array($var))
        {
            $dump = $this->doDumpArray($var, $maxDepth, $maxCharacter, $dumpContent, $displayTrace, $depth);
        }
        // object
        elseif(is_object($var))
        {
            $dump = $this->doDumpObject($var, $maxDepth, $maxCharacter, $dumpContent, $displayTrace, $depth);
        }
        return $dump;
    }
    
    /**
     *
     * @param mixed $var
     * @param int $maxCharacter
     * 
     * @return array 
     */
    protected function doDumpScalar($var , $maxCharacter = null)
    {
        $dump = array();
        if (is_int($var))
        {
            $dump = array('type' => 'int', 'value' => $var);
        }
        elseif (is_float($var)) 
        {
            $dump = array('type' => 'float', 'value' => $var);
        }
        elseif (is_string($var))
        {
            $length = strlen($var);
            if (!is_null($maxCharacter) && is_numeric($maxCharacter) && $length > $maxCharacter)
            {
                $var = substr($var, 0, $maxCharacter);
            }
            $dump = array('type' => 'string', 'value' => $var, 'length' => $length, 'max_length' => $maxCharacter);
        }
        elseif (is_bool($var)) 
        {
            $dump = array('type' => 'bool', 'value' => $var);
        }
        return $dump;
    }
    
    /**
     * dump an array
     *
     * @access protected
     *
     * @param array $array
     * @param int $maxDepth default = 4
     * @param int $maxCharacter = null max character to show if the variable is a string
     * @param bool $dumpContent default = true, if true dump the content of an array or a object
     * @param bool $displayTrace default = true, if true and the dumped var is an exception it displays the Exception backtrace
     * @param int $depth default = 0, first call DO NOT set this parameter !!
     *
     * @return string
     */
    protected function doDumpArray(array $array, $maxDepth = 4, $maxCharacter = null, $dumpContent = true, $displayTrace = true, $depth = 0)
    {
        $length = count($array);
        
        $values = array();
        if($dumpContent && $depth < $maxDepth)
        {
            foreach($array as $key => $value)
            {
                $values[$key] = $this->doDump($value, $maxDepth, $maxCharacter, $dumpContent, $displayTrace, $depth+1);
            }
        }
        else
        {
            $values = '...';
        }
        
        $dump = array(
            'type' => 'array', 
            'composite' => true,
            'value' => $values, 
            'length' => $length
        );
        
        return $dump;
    }
    
    /**
     * dump an object
     *
     * @access protected
     *
     * @param object $object
     * @param int $maxDepth default = 4
     * @param int $maxCharacter = null max character to show if the variable is a string
     * @param bool $dumpContent default = true, if true dump the content of an array or a object
     * @param bool $displayTrace default = true, if true and the dumped var is an exception it displays the Exception backtrace
     * @param int $depth default = 0, first call DO NOT set this parameter !!
     *
     * @return string
     */
    protected function doDumpObject($object, $maxDepth = 4, $maxCharacter = null, $dumpContent = true, $displayTrace = true, $depth = 0)
    {
        $isException = $object instanceof Exception;

        $reflexionObject = new ReflectionObject($object);
        $propertiesList = array();
        
        if ($dumpContent)
        {
            if($depth < $maxDepth)
            {
                $properties = $reflexionObject->getProperties();
                foreach($properties as $property)
                {
                    $propertiesList[$property->getName()] .= $this->doDumpObjectProperty($property, $object, $maxDepth-1, $maxCharacter, $displayTrace, $depth)."\n";
                }
                
                 if ($isException)
                 {
                     $propertiesList['trace'] = array(
                         'type' => 'backtrace', 
                         'max_char' => isset($maxCharacter) ? (int) $maxCharacter : 180, 
                         'value' => $displayTrace ? $object->getTrace() : null,
                     );
                     
                     if (method_exists($object, 'getPrevious') && $object->getPrevious() != null)
                     {
                         $propertiesList['previous'] = $this->doDumpObject($object->getPrevious(), $maxDepth, $maxCharacter, $dumpContent, $displayTrace, $depth+1);
                     }
                 }
            }
        }
        $dump = array(
            'type' => $isException ? 'exception' : 'object', 
            'composite' => true,
            'class' => $reflexionObject->getName(), 
            'properties' => $propertiesList);
        return $dump;
    }

    /**
     * dump object property
     *
     * @access protected
     *
     * @param ReflectionProperty $property
     * @param int $maxDepth default = 4
     * @param int $maxCharacter = null max character to show if the variable is a string
     * @param bool $displayTrace default = true, if true and the dumped var is an exception it displays the Exception backtrace
     * @param int $depth default = 0, first call DO NOT set this parameter !!
     *
     * @return string
     */
    protected function doDumpObjectProperty(ReflectionProperty $property, $object, $maxDepth = 4, $maxCharacter = null, $displayTrace = true, $depth = 0)
    {
        $access = '';
        if ($property->isPublic())
        {
            $access = 'public';
        }
        elseif ($property->isPrivate())
        {
            $access = 'private';
        }
        elseif ($property->isProtected())
        {
            $access = 'protected';
        }

        if($property->isPublic()) 
        {
            $value = $this->doDump($property->getValue($object), $maxDepth, $maxCharacter, true, $displayTrace, $depth+1);
        }
        // in PHP >= 5.3 can retrieve private or protected value
        elseif(method_exists($property,'setAccessible'))
        {
            $property->setAccessible(true);
            $value = $this->doDump($property->getValue($object), $maxDepth, $maxCharacter, true, $displayTrace, $depth+1);
        }
        elseif(!$property->isStatic() && method_exists($object, '__get'))
        {
            $propertyName = $property->getName();
            $value = $object->$propertyName;
            $value = $this->doDump($value, $maxDepth, $mode, $maxCharacter, true, $displayTrace, $depth+1);
        }
        //TODO doesn't work properly if the class has many properties with same name but different cases
        //    elseif(!$property->isStatic() && method_exists($object, 'get'.ucFirst($property->getName())))
        //    {
        //       $value = call_user_func(array($object, 'get'.ucFirst($property->getName())));
        //       $value = $this->doDump($value, $maxDepth, true, $maxCharacter, $depth+1);
        //    }
        else
        {
            $value = self::PHP_NO_ACCESS;
        }

        $dump = array(
            'type' => 'property', 
            'composite' => false, 
            'access' => $access, 
            'value' => $value
        );
        
        return $dump;
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
        if (is_null($var))
        {
            return 'NULL';
        }

        if (is_bool($var))
        {
            return $var ? 'TRUE' : 'FALSE';
        }

        if(is_resource($var))
        {
            $return = get_resource_type($var);
            return (empty($return) || $return == 'Unknown') ? 'Unknown Resource' : $return;
        }

        if (is_object($var))
        {
            if (method_exists($var, '__toString'))
            {
                return $var->__toString();
            }
            return 'Object '.get_class($var);
        }

        return (string) $var;
    }
    
    /**
     * get information about file and line where the dump function was called
     *
     * @access public
     *
     * @param string $functionName default = ''
     * @param string $className default = null
     *
     * @return array
     */
    public function getCallInfos($functionName = null, $className = null)
    {
        $call = array();
        if (is_null($functionName))
        {
            $functionName = __FUNCTION__;
        }
        if (is_null($className))
        {
            $className = __CLASS__;
        }
        
        if (class_exists($className) && method_exists($className, $functionName))
        {
            $trace = debug_backtrace(false);
            while($callTrace = array_shift($trace))
            {
                if ($callTrace['function'] == $functionName && $callTrace['class'] == $className)
                {
                    break;
                }
            }
        }
        elseif (function_exists($functionName))
        {
            $trace = debug_backtrace();
            while($callTrace = array_shift($trace))
            {
                if ($callTrace['function'] == $functionName && (!isset($callTrace['class']) || $callTrace['class'] == '') )
                {
                    break;
                }
            }
        }
       
        if (isset($callTrace['file']) && isset($callTrace['line']))
        {
            $call = array(
                'file' => $callTrace['file'],
                'line' => $callTrace['line'],
            );
        }
        
        return $call;
    }
}
