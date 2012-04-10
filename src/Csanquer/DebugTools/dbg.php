<?php
namespace Csanquer\DebugTools;

use Csanquer\DebugTools\Debug;

/**
 * Debug Helper Utility class
 *
 * @author Charles SANQUER
 *
 */
class dbg
{

    /**
     * return or print a variable dump with custom colorized HTML display like xdebug
     *
     * @access public
     * @static
     *
     * @param mixed $var
     * @param string $name default = null, variable name to display
     * @param int $maxDepth default = 4, max recursion iteration
     * @param bool $return default = true, if true return, else echo the result
     * @param array $options default = array(), available options : <br/>
     *   - bool show_content = true, display content of array and object <br/>
     *   - bool show_trace default = true, if true and the dumped var is an exception it displays the Exception backtrace <br/>
     *   - int max_char = null, max character to show if the variable is a string <br/>
     *   - int mode default = null, available render modes (CLI , CLI with color, HTML) <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string or void
     */
    public static function dump($var, $name = null, $maxDepth = 4, $return = true, array $options = array())
    {
        $debug = new Debug();
        return $debug->dump($var, $name, $maxDepth, $return, $options);
    }

    /**
     * print_r to HTML if PHP is running in cli mode
     * return print_r result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     * @static
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param array $options default = array(), available options : <br/>
     *   - int mode default = null, available render modes (CLI , CLI with color, HTML) <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string or void
     */
    public static function print_r($var, $name = null, $return = false, array $options = array())
    {
        $debug = new Debug();
        return $debug->print_r($var, $name, $return, $options);
    }

    /**
     * var_dump to HTML if PHP is running in cli mode
     * return var_dump result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     * @static
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param array $options default = array(), available options : <br/>
     *   - int mode default = null, available render modes (CLI , CLI with color, HTML) <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string or void
     */
    public static function var_dump($var, $name = null, $return = false, array $options = array())
    {
        $debug = new Debug();
        return $debug->var_dump($var, $name, $return, $options);
    }

    /**
     * debug_zval_dump to HTML if PHP is running in HTML mode
     * return debug_zval_dump result as an HTML string (default) or echo print_r as HTML
     *
     * @access public
     * @static
     *
     * @param mixed $var variable to dump
     * @param string $name default = null, variable name to display
     * @param bool $return default = false, if true return, else echo the result
     * @param array $options default = array(), available options : <br/>
     *   - int mode default = null, available render modes (CLI , CLI with color, HTML) <br/>
     *   - string function default = this function's name, function name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *   - string class default = null, class name to search from backtrace, optional only use if you wrapped Debug class <br/>
     *
     * @return string or void
     */
    public static function zval_dump($var, $name = null, $return = false, array $options = array())
    {
        $debug = new Debug();
        return $debug->zval_dump($var, $name, $return, $options);
    }

    /**
     * return the backtrace as an HTML array
     *
     * @access public
     * @static
     *
     * @param bool $return default = false, if true return, else echo the result
     * @param array $options default = array(), available options : <br/>
     *   - int max_char = 180, max character to show if the variable is a string <br/>
     *   - string mode default = null, available render modes (CLI , CLI with color, HTML) <br/>
     *
     * @return string or void
     */
    public static function backtrace($return = false, array $options = array())
    {
        $debug = new Debug();
        return $debug->backtrace($return, $options);
    }


    /**
     * Simple Conversion from variable to String representation
     *
     * @access public
     * @static
     *
     * @param mixed $var variable to dump
     * @return string
     */
    public static function asString($var)
    {
        $debug = new Debug();
        return $debug->asString($var);
    }

}
?>
