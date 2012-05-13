<?php

namespace Csanquer\DebugTools;

use Csanquer\DebugTools\Output\OutputInterface;
use Csanquer\DebugTools\Output\OutputFactory;
use \ReflectionObject;
use \ReflectionProperty;
use \Exception;

/**
 * Debugging tool class
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Dumper
{

    const PHP_NO_ACCESS = 'php_no_access';

    /**
     * var_export to dump infos array
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *
     * @return string|array 
     */
    public function var_export($var, $name = null, array $options = array())
    {
        return array(
            'name' => $name,
            'type' => 'var_export',
            'composite' => is_array($var) || is_object($var),
            'value' => var_export($var, true),
            'call' => $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
    }

    /**
     * print_r to dump infos array
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *
     * @return string|array 
     */
    public function print_r($var, $name = null, array $options = array())
    {
        return array(
            'name' => $name,
            'type' => 'print_r',
            'composite' => is_array($var) || is_object($var),
            'value' => print_r($var, true),
            'call' => $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
    }

    /**
     * var_dump to dump infos array
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *
     * @return string|array 
     */
    public function var_dump($var, $name = null, array $options = array())
    {
        ob_start();
        var_dump($var);
        $value = ob_get_clean();
        $value = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $value);

        $xdebugEnabled = extension_loaded('xdebug');

        return array(
            'name' => $name,
            'type' => 'var_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $value,
            'call' => $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
    }

    /**
     * debug_zval_dump to dump infos array
     *
     * @access public
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param array $options default = array(), available options : <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *
     * @return string|array 
     */
    public function zval_dump($var, $name = null, array $options = array())
    {
        ob_start();
        debug_zval_dump($var);
        $value = ob_get_clean();
        $value = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $value);

        return array(
            'name' => $name,
            'type' => 'zval_dump',
            'composite' => is_array($var) || is_object($var),
            'value' => $value,
            'call' => $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
    }

    /**
     * return the backtrace to backtrace infos array
     *
     * @access public
     *
     * @param array $options default = array(), available options : <br/>
     *   - int max_char = 180, max character to show if the variable is a string <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *
     * @return string|array 
     */
    public function backtrace(array $options = array())
    {
        $maxCharacter = isset($options['max_char']) ? (int) $options['max_char'] : 180;
        return array(
            'name' => 'backtrace',
            'type' => 'backtrace',
            'composite' => true,
            'max_char' => $maxCharacter,
            'value' => $this->doDumpBacktrace(debug_backtrace(), $maxCharacter),
            'call' => $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class'])
        );
    }

    /**
     *
     * @param array $trace debug_backtrace array
     * @param int $maxCharacter
     * @return array 
     */
    protected function doDumpBacktrace(array $trace, $maxCharacter = null)
    {
        $dump = array();
        foreach ($trace as $call => $row)
        {
            $rowDump = array();
            if (isset($row['function']))
            {
                $rowDump['function'] = $row['function'];
            }
            
            if (isset($row['line']))
            {
                $rowDump['line'] = $row['line'];
            }
            
            if (isset($row['file']))
            {
                $rowDump['file'] = $row['file'];
            }
            
            if (isset($row['type']))
            {
                $rowDump['type'] = $row['type'];
            }
            
            if (isset($row['args']))
            {
                foreach ($row['args'] as $arg)
                {
                    $rowDump['args'][] = $this->doDump($arg, 1, $maxCharacter, false, false, 0);
                }
            }
            $dump[] = $rowDump;
        }
        return $dump;
    }
    
    /**
     *  custom dump to dump infos array
     * 
     * @param mixed $var
     * @param string $name default = null, variable name to display
     * @param int $maxDepth default = 4
     * @param array $options default = array(), available options : <br/>
     *   - bool dump_content = true, display content of array and object <br/>
     *   - bool with_trace default = true, if true and the dumped var is an exception it displays the Exception backtrace <br/>
     *   - int max_char = 180, max character to show if the variable is a string <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Dumper class <br/>
     * 
     * @return string|array 
     */
    public function dump($var, $name = null, $maxDepth = 4, array $options = array())
    {
        $dump = $this->doDump(
                $var, 
                is_numeric($maxDepth) && $maxDepth >= 0 ? $maxDepth : 4, 
                isset($options['max_char']) ? (int) $options['max_char'] : null, 
                isset($options['dump_content']) ? (bool) $options['dump_content'] : true, 
                isset($options['with_trace']) ? (bool) $options['with_trace'] : true, 
                0
        );

        $dump['name'] = $name;
        $dump['call'] = $this->getCallInfos(empty($options['function']) ? __FUNCTION__ : $options['function'], empty($options['class']) ? null : $options['class']);

        return $dump;
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
                'type' => 'resource',
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
        elseif (is_object($var))
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
    protected function doDumpScalar($var, $maxCharacter = null)
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
        $dump['composite'] = false;
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
        if ($dumpContent && $depth < $maxDepth)
        {
            foreach ($array as $key => $value)
            {
                $values[$key] = $this->doDump($value, $maxDepth, $maxCharacter, $dumpContent, $displayTrace, $depth + 1);
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
            if ($depth < $maxDepth)
            {
                $properties = $reflexionObject->getProperties();
                foreach ($properties as $property)
                {
                    if (!$isException || $isException && $property->getName() != 'previous' && $property->getName() != 'trace')
                    {
                        $propertiesList[$property->getName()] = $this->doDumpObjectProperty($property, $object, $maxDepth - 1, $maxCharacter, $displayTrace, $depth);
                    }
                }

                if ($isException)
                {
                    if ($displayTrace)
                    {
                        $maxChars = isset($maxCharacter) ? (int) $maxCharacter : 180;
                        $propertiesList['trace'] = array(
                            'type' => 'backtrace',
                            'composite' => true,
                            'max_char' => $maxChars,
                            'value' => $this->doDumpBacktrace($object->getTrace(), $maxChars),
                        );
                    }
                    
                    if (method_exists($object, 'getPrevious') && $object->getPrevious() instanceof Exception)
                    {
                        $propertiesList['previous'] = $this->doDumpObject($object->getPrevious(), $maxDepth, $maxCharacter, $dumpContent, false, $depth + 1);
                    }
                }
            }
        }
        
        $dump = array(
            'type' => $isException ? 'exception' : 'object',
            'composite' => true,
            'class' => $reflexionObject->getName(),
            'properties' => $propertiesList,
        );
        
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

        if ($property->isPublic())
        {
            $value = $this->doDump($property->getValue($object), $maxDepth, $maxCharacter, true, $displayTrace, $depth + 1);
        }
        // in PHP >= 5.3 can retrieve private or protected value
        else
        {
            $property->setAccessible(true);
            $value = $this->doDump($property->getValue($object), $maxDepth, $maxCharacter, true, $displayTrace, $depth + 1);
        }
        /**
        // in PHP >= 5.3 can retrieve private or protected value
        elseif (method_exists($property, 'setAccessible'))
        {
            $property->setAccessible(true);
            $value = $this->doDump($property->getValue($object), $maxDepth, $maxCharacter, true, $displayTrace, $depth + 1);
        }
        elseif (!$property->isStatic() && method_exists($object, '__get'))
        {
            $propertyName = $property->getName();
            $value = $object->$propertyName;
            $value = $this->doDump($value, $maxDepth, $mode, $maxCharacter, true, $displayTrace, $depth + 1);
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
/**/
        $dump = array(
            'name' => $property->getName(),
            'type' => 'property',
            'composite' => false,
            'access' => $access,
            'static' => $property->isStatic(),
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

        if (is_resource($var))
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
            return 'Object ' . get_class($var);
        }

        return (string) $var;
    }

    /**
     * get information about file and line where the dump function was called
     *
     * @access public
     *
     * @param string $functionName default = getCallInfos
     * @param string $className default = \Csanquer\DebugTools\Dumper
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
            while ($callTrace = array_shift($trace))
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
            while ($callTrace = array_shift($trace))
            {
                if ($callTrace['function'] == $functionName && (!isset($callTrace['class']) || $callTrace['class'] == ''))
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
