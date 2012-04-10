<?php

namespace Csanquer\DebugTools\Output;

/**
 * Description of HtmlOutput
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Html extends AbstractOutput
{
    /**
     *
     * CSS Style Array
     * @var array
     */
    protected $cssStyle = array(
        'box' => 'margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Helvetica,Arial,verdana,sans-serif; border: 1px solid #cccccc; padding: 5px; font-size: 11px; line-height: 13px;',
        'varName' => 'color: #800000;',
        'table' => array(
            'cell' => 'border: solid 1px #c1c1c1;',
            'cellCall' => 'border: solid 1px #c1c1c1; font-size: x-small; font-weight: bold; color: #000000;',
            'header' => 'background-color: #d0cece; text-align: center;',
            'lineAlt' => 'background-color: #e7e7e7;',
        ),
        'typeLabel' => array(
            'all' => 'font-size: x-small; font-weight: bold; color: #000000;',
            'exception' => 'color: #525252;',
            'length' => 'font-style: italic; color: #525252;',
            'callFile' => 'font-size: x-small; font-weight: bold; color: #000000;',
            'callLine' => 'font-size: x-small; font-weight: bold; color: #cc0000;',
        ),
        'type' => array(
            'int' => 'color: #4e9a06;',
            'float' => 'color: #f57900;',
            'string' => 'color: #cc0000;',
            'bool' => 'color: #760175;',
            'null' => 'color: #3465a4;',
            'resource' => 'color: #800080;',
            'class' => 'color: #0000a9;',
            'exception' => 'color: #C51C0A;',
        ),
        'object' => array(
            'static' => 'color: #1b6160;',
            'access' => 'font-style: italic;',
            'public' => 'color: #005500;',
            'protected' => 'color: #b17500;',
            'private' => 'color: #b00000;',
        )
    );
    
    protected static $echoCSS = false;
    
    protected $css;

    public function format($dump)
    {
        
    }
}

