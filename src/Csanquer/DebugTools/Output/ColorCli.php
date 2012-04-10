<?php

namespace Csanquer\DebugTools\Output;

/**
 * Color CLI Formatter
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class ColorCli extends AbstractCli
{
    /**
     *
     * ansi color and style codes array
     * @var array
     */
    protected $ansiCodes = array (
        'foreground' => array(
            'black'        => '0;30',
            'dark_grey'    => '1;30',
            'red'          => '0;31',
            'light_red'    => '1;31',
            'green'        => '0;32',
            'light_green'  => '1;32',
            'brown'        => '0;33',
            'yellow'       => '1;33',
            'blue'         => '0;34',
            'light_blue'   => '1;34',
            'purple'       => '0;35',
            'light_purple' => '1;35',
            'cyan'         => '0;36',
            'light_cyan'   => '1;36',
            'light_grey'   => '0;37',
            'white'        => '1;37',
            'reset'        => '0',
        ),
        'background' => array(
            'black'        => '40',
            'red'          => '41',
            'green'        => '42',
            'yellow'       => '43',
            'blue'         => '44',
            'purple'       => '45',
            'cyan'         => '46',
            'grey'         => '47',
            'reset'        => '0',
        )
    );

    /**
     * ansi color Style Array
     * @var array
     */
    protected $ansiStyle = array(
        'box' => array('fg' => 'light_grey', 'bg' => null),
        'varName' => array('fg' => 'brown', 'bg' => null),
        'table' => array(
              'cell' => array('fg' => null, 'bg' => null),
              'cellCall' => array('fg' => null, 'bg' => null),
              'header' => array('fg' => null, 'bg' => null),
              'lineAlt' => array('fg' => null, 'bg' => null),
        ),
        'typeLabel' => array(
          'all' => array('fg' => 'light_grey', 'bg' => null),
          'exception' => array('fg' => 'light_grey', 'bg' => 'red'),
          'length' => array('fg' => 'dark_grey', 'bg' => null),
          'callFile' => array('fg' => 'light_grey', 'bg' => null),
          'callLine' => array('fg' => 'red', 'bg' => null),
        ),
        'type' => array(
          'int' => array('fg' => 'green', 'bg' => null),
          'float' => array('fg' => 'light_green', 'bg' => null),
          'string' => array('fg' => 'red', 'bg' => null),
          'bool' => array('fg' => 'light_purple', 'bg' => null),
          'null' => array('fg' => 'cyan', 'bg' => null),
          'resource' => array('fg' => 'purple', 'bg' => null),
          'class' => array('fg' => 'light_blue', 'bg' => null),
          'exception' => array('fg' => 'light_green', 'bg' => 'red'),
        ),
        'object' => array(
          'static' => array('fg' => 'light_blue', 'bg' => null),
          'access' => array('fg' => null, 'bg' => null),
          'public' => array('fg' => 'green', 'bg' => null),
          'protected' => array('fg' => 'yellow', 'bg' => null),
          'private' => array('fg' => 'red', 'bg' => null),
        )
    ); 

    protected function applyStyle($string, $style = null)
    {
        
    }
}
