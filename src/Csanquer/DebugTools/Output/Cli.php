<?php

namespace Csanquer\DebugTools\Output;

/**
 * CLI Formatter
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Cli extends AbstractCli
{
    protected function applyStyle($string, $style = null)
    {
        return $string;
    }
}

