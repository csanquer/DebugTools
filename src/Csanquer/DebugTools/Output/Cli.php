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

    protected function formatDump($dump, $depth = 0)
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
            
            case 'array':
                $output .= $this->formatArray($dump, $depth);
                break;
            case 'exception':
                $output .= $this->formatException($dump, $depth);
                break;
            
            case 'object':
                $output .= $this->formatObject($dump, $depth);
                break;
            
            case 'property':
                $output .= $this->formatProperty($dump, $depth);
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
    
    protected function formatArray($dump, $depth = 0)
    {
        $output = $this->applyStyle('array', 'type_array_label').' '.
            $this->applyStyle('(length = ', 'type_array_lengthlabel').
            $this->applyStyle($dump['length'], 'type_array_length').
            $this->applyStyle(')', 'type_array_lengthlabel');

        if (is_array($dump['value']))
        {
            $output .= ' {'."\n";

            foreach($dump['value'] as $key => $dumpKey)
            {
                $output .= str_repeat($this->getDefaultIndentChar(),($depth+1) * $this->getDefaultIndentNumber()).
                    '['.(is_int($key)? $this->applyStyle($key, 'type_int') : $this->applyStyle('\''.$key.'\'', 'type_string') ).'] => '.
                    $this->formatDump($dumpKey, $depth+1)."\n";
            }
            $output .= str_repeat($this->getDefaultIndentChar(), $depth * $this->getDefaultIndentNumber()).'}';
        }
        else
        {
            $output .= ' ...';
        }
        return $output;
    }
    
    protected function formatObject($dump, $depth = 0)
    {
        $output = $this->applyStyle('object','typeLabel_all').' '.$this->applyStyle($dump['class'],'type_class');

        $output .=' {'."\n";
        if(is_array($dump['properties']) && !empty($dump['properties']))
        {
            foreach($dump['properties'] as $name => $property)
            {
                $output .= str_repeat($this->getDefaultIndentChar(),($depth+1) * $this->getDefaultIndentNumber()).
                    $this->formatProperty($property, $depth+1)."\n";
            }
        }
        else
        {
            $output .= str_repeat($this->getDefaultIndentChar(),($depth+1) * $this->getDefaultIndentNumber()).'...'."\n";
        }
        $output .= str_repeat($this->getDefaultIndentChar(), $depth * $this->getDefaultIndentNumber()).'}';
        return $output;
    }
        
    protected function formatProperty($dump, $depth = 0)
    {
        return ($dump['access'] != '' ? $this->applyStyle($dump['access'],'object_'.$dump['access']).' ' : '').
            ($dump['static'] ? $this->applyStyle('static','object_static').' ' : '').
            $this->applyStyle('\''.$dump['name'].'\'', 'varName').' '.
            $this->formatDump($dump['value'], $depth);
    }
    
    protected function formatException($dump, $depth = 0)
    {
        
    }
    
    protected function formatBacktrace($trace)
    {
        $output = '';
        var_dump($trace);
        return $output;
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
            
            $output .= $rowDump ."\n";
        }

        return $output;
    }
    
    protected function formatCallInfos(array $callTrace)
    {
        return 'Called from '.
            $this->applyStyle($callTrace['file'], 'typeLabel_callFile').
            ' on line '.
            $this->applyStyle($callTrace['line'], 'typeLabel_callLine');
    }
}

