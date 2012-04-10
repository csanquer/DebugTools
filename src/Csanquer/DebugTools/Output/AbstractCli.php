<?php

namespace Csanquer\DebugTools\Output;

/**
 * AbstractCli
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
abstract class AbstractCli extends AbstractOutput
{

    public function format($dump)
    {
        $output = $this->createSeparatorLine().
            $this->formatName($dump['name'], $dump['composite']);
        
        switch ($dump['type'])
        {
            case 'groups' :
                break;
                
            case 'var_dump':
            case 'print_r':
            case 'zval_dump':
            case 'var_export':
                
                break;
            
            case 'backtrace':
                break;
            
            case 'int':
            case 'float':
            case 'bool':
            case 'null':
                break;
            
            case 'string':
                break;
            
            case 'ressource':
                break;
            
            case 'exception':
                break;
            
            case 'object':
                break;
            
            case 'property':
                break;
            
        }
        
        return $output.
            $this->formatCallInfos($dump['call']).
            $this->createSeparatorLine();
    }
    
    abstract protected function applyStyle($string, $style = null);
    
    protected function createSeparatorLine($char = '-', $number = 80)
    {
        $sepline = '';
        for ($i=0; $i<$number; $i++)
        {
            $sepline.= $char;
        }
        return "\n".$this->applyStyle($sepline, 'box')."\n";
    }

    protected function formatName($name, $dumpNewLine = false)
    {
        if (!empty($name))
        {
            return $this->applyStyle($name, 'varName').' = '.($dumpNewLine ? "\n" : '');
        }
        return '';
    }
    
    protected function formatArray($dump)
    {
        
    }
    
    protected function formatObject($dump)
    {
        
    }
    
    protected function formatProperty($dump)
    {
        
    }
    
    protected function formatBacktrace($dump)
    {
        
    }
    
    protected function formatException($dump)
    {
        
    }
    
    protected function formatCallInfos(array $callTrace)
    {
        return "\n".'Called from '.
            $this->applyStyle($callTrace['file'], 'typeLabel_callFile').
            ' : line '.
            $this->applyStyle($callTrace['line'], 'typeLabel_callLine');
    }
}

