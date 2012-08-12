<?php
namespace Csanquer\DebugTools;

use Csanquer\DebugTools\Debug;

/**
 *
 * Singleton Debugger class to help debugging
 *
 * @author Charles SANQUER
 *
 */
class Debugger
{
    /**
     *
     * @var Debugger
     */
    private static $instance;

    /**
     *
     * @var \DebugTools\Debug
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $output;

    /**
     * outside instanciation is forbidden
     *
     * @access private
     */
    private function __construct()
    {
        $this->reset();
        $this->debug = new Debug();
        $this->output = '';
        $this->setEnabled(false);

    }

    /**
     * cloning raise an error
     *
     * @throws ErrorException
     */
    public function __clone()
    {
        throw new ErrorException('Cloning is forbidden : this class follow the design pattern singleton.');
    }

    /**
     * retrieve singleton Debugger instance
     *
     * @return Debugger single instance
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;

            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * check if singleton Debugger has an instance
     *
     * @return bool
     */
    public static function hasInstance()
    {
        return isset(self::$instance) && (self::$instance instanceof Debugger);
    }

    /**
     * reset Singleton Instance
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * to en/disable debug
     * @param bool $value
     */
    public function setEnabled($value)
    {
        $this->enabled = (bool) $value;
    }

    /**
     * check if debugger is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * @return string|null debug content, null is debug is disabled
     */
    public function render()
    {
        if ($this->enabled) {
            return $this->output;
        }

        return null;
    }

    /**
     * reset debug content
     */
    public function reset()
    {
        $this->debug = new Debug();
        $this->output = '';
    }

    /**
     * check if PHP is running in CLI mode
     *
     * @access protected
     * @static
     *
     * @return bool
     */
    protected function isCli()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * append new debug content
     *
     * @param string $output
     * @param bool   $withNewLine default = true
     */
    public function append($output, $withNewLine = true)
    {
        if ($this->enabled) {
            if (empty($output)) {
                $output = '';
            }
            $this->output .= (string) ($output);

            if ($withNewLine) {
                $this->output .=$this->isCli() ? PHP_EOL : '<br/>';
            }
        }
    }

    public function group($groupname)
    {

    }

    public function endGroup()
    {

    }

    /**
     * dump a variable with xdebug-like var_dump
     *
     * @param mixed  $var
     * @param string $name         default : NULL
     * @param int    $maxDepth     max recursion
     * @param int    $maxCharacter = null max character to show if the variable is a string
     */
    public function dump($var, $name = null, $maxDepth = 4, $maxCharacter = null)
    {
        $this->append($this->debug->dump($var, $name, $maxDepth, true, array('max_char' => $maxCharacter, 'function' => __FUNCTION__, 'class' => __CLASS__)), false);
    }

    /**
     * dump a variable with print_r
     *
     * @param mixed  $var
     * @param string $name default : NULL
     */
    public function print_r($var, $name = null)
    {
        $this->append($this->debug->print_r($var, $name, true, array('function' => __FUNCTION__, 'class' => __CLASS__)), false);
    }

    /**
     * dump a variable with original var_dump
     *
     * @param mixed  $var
     * @param string $name default : NULL
     */
    public function var_dump($var, $name = null)
    {
        $this->append($this->debug->var_dump($var, $name, true, array('function' => __FUNCTION__, 'class' => __CLASS__)), false);
    }

    /**
     * dump a variable with original zval_dump
     *
     * @param mixed  $var
     * @param string $name default : NULL
     */
    public function zval_dump($var, $name = null)
    {
        $this->append($this->debug->zval_dump($var, $name, true, array('function' => __FUNCTION__, 'class' => __CLASS__)), false);
    }

    /**
     * export a variable with original var_export
     *
     * @param mixed  $var
     * @param string $name default : NULL
     */
    public function var_export($var, $name = null)
    {
        $this->append($this->debug->var_export($var, $name, true, array('function' => __FUNCTION__, 'class' => __CLASS__)), false);
    }
    /**
     *
     * return the backtrace as an HTML array
     *
     - @param int $maxCharacter = null max character to show if the variable is a string
     */
    public function backtrace($maxCharacter = null)
    {
        $this->append($this->debug->backtrace(true, array('max_char' => $maxCharacter)), false);
    }
}
