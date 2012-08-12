<?php

namespace Csanquer\DebugTools\Output;

use Csanquer\DebugTools\Output\Cli;
use Csanquer\DebugTools\Output\ColorCli;
use Csanquer\DebugTools\Output\Html;

/**
 * Description of OutputFactory
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class OutputFactory
{
    const MODE_CLI = 'cli';
    const MODE_COLOR_CLI = 'color_cli';
    const MODE_HTML = 'html';
    const MODE_NO_FORMAT = 'no_format';

    /**
     *
     * @var bool
     */
    protected static $isCli;

    /**
     *
     * @var bool
     */
    protected static $canSupportColor;

    /**
     *
     * @param string $mode default = null , if null the mode will be guessed
     *
     * @return \DebugTools\Output\OutputInterface
     */
    public function createOutput($mode = null)
    {
        $mode = $this->checkRenderMode($mode);

        switch ($mode) {
            case self::MODE_NO_FORMAT:
                $output = null;
                break;

            case self::MODE_HTML:
                $output = new Html();
                break;

            case self::MODE_COLOR_CLI:
                $output = new ColorCli();
                break;

            case self::MODE_CLI:
            default:
                $output = new Cli();
        }

        return $output;
    }

    /**
     *
     * @return array
     */
    public static function getAvailableOutput()
    {
        return array(
            self::MODE_CLI,
            self::MODE_COLOR_CLI,
            self::MODE_HTML,
            self::MODE_NO_FORMAT,
        );
    }

    /**
     * check if PHP is running in CLI mode
     *
     * @codeCoverageIgnore
     * @access protected
     *
     * @return bool
     */
    protected function isCli()
    {
        if (is_null(self::$isCli)) {
            self::$isCli = php_sapi_name() == 'cli';
        }

        return self::$isCli;
    }

    /**
     * check PHP CLI is running on terminal that support ansi color escape codes
     *
     * @codeCoverageIgnore
     * @access protected
     *
     * @return bool
     */
    protected function canSupportColor($stream = STDOUT)
    {
        if (is_null(self::$canSupportColor)) {
            self::$canSupportColor = DIRECTORY_SEPARATOR != '\\' && function_exists('posix_isatty') && @posix_isatty($stream);
        }

        return self::$canSupportColor;
    }

    /**
     * check if mode is provided and correct and return a correct mode constant
     *
     * @access protected
     *
     * @param int|string $mode default = null, available render modes (CLI , CLI with color, HTML)
     *
     * @return int mode (CLI, COLOR_CLI , HTML)
     */
    protected function checkRenderMode($mode = null)
    {
        // force render mode
        if (is_null($mode) || !in_array(strtolower($mode), $this->getAvailableOutput())) {
            $mode = self::MODE_CLI;
            if (!$this->isCli()) {
                $mode = self::MODE_HTML;
            } elseif ($this->isCli() && $this->canSupportColor()) {
                $mode = self::MODE_COLOR_CLI;
            }
        }

        return $mode;
    }
}
