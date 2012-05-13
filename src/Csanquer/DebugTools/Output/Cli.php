<?php

namespace Csanquer\DebugTools\Output;

/**
 * Cli
 *
 * @author Charles SANQUER <charles.sanquer@gmail.com>
 */
class Cli extends AbstractOutput
{

    /**
     *
     * @param array $dump
     * @return string 
     */
    public function format($dump)
    {
        return 
            $this->createSeparatorLine().
            $this->formatName($dump['name'], $dump['composite']).
            $this->formatDump($dump).
            "\n".$this->formatCallInfos($dump['call']).
            $this->createSeparatorLine();
    }
    
    protected function applyStyle($string, $style = null)
    {
        return $string;
    }
    
    protected function createSeparatorLine($char = '-', $number = 80)
    {
        return "\n".$this->applyStyle(str_repeat($char, $number), 'box')."\n";
    }

    protected function formatDump($dump)
    {
        $output = '';
        $type = strtolower($dump['type']);
        switch ($type)
        {
            case 'var_dump':
            case 'print_r':
            case 'zval_dump':
            case 'var_export':
                $output .= $dump['value'];
                break;
            
            case 'backtrace':
                $output .= $this->formatBacktrace($dump['value']);
                break;
            
            case 'int':
            case 'float':
                $output .= $this->applyStyle($dump['type'].' '.$dump['value'], $type);
                break;
            case 'bool':
                $output .= $this->applyStyle($dump['type'].' '.($dump['value'] ? 'true' : 'false'), $type);
                break;
            
            case 'null':
                $output .= $this->applyStyle('NULL', 'null');
                break;
            
            case 'string':
                $output .= $this->applyStyle($dump['type'].' (length = '.$dump['length'].') '.'\''.$dump['value'].'\'', $type);
                break;
            
            case 'resource':
                $output .= $this->applyStyle(ucfirst($dump['type']).'('.$dump['value'].') of type '.$dump['resource_type'], $type);
                break;
            
            case 'exception':
                break;
            
            case 'object':
                break;
            
            case 'property':
                break;
            
        }
        return $output;
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
    
    protected function formatException($dump)
    {
        
    }
    
    protected function formatBacktrace($trace)
    {
        $dump = '';
        var_dump($trace);
        return $dump;
        foreach ($trace as $call => $row)
        {
            $rowDump = '# ';
            $rowDump .= $this->applyStyle($call, '');
            $rowDump .='   ';

            $function= '';
            if (!empty($row['class']))
            {
                $function = $row['class'].$row['type'];
            }
            
            $function .= $row['function'].'(';

            if (!empty($row['args']))
            {
                $firstArg = true;
                foreach($row['args'] as $arg)
                {
                    if ($firstArg)
                    {
                        $firstArg = false;
                    }
                    else
                    {
                        $function .=', ';
                    }
                    $function .= $this->formatDump($arg);
                }
            }
            $function .= ')';

            $rowDump .= $this->applyStyle($function, '');
            $rowDump .='   ';

            if (isset($row['file']))
            {
                $rowDump .= $this->formatCallInfos(array('file' => $row['file'], 'line' => $row['line']));
            }
            
            $dump .= $rowDump ."\n";
        }

        return $dump;
    }
    
    protected function formatCallInfos(array $callTrace)
    {
        return 'Called from '.
            $this->applyStyle($callTrace['file'], 'typeLabel_callFile').
            ' on line '.
            $this->applyStyle($callTrace['line'], 'typeLabel_callLine');
    }
}

