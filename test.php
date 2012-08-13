#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

function testDumpException()
{
    $dump = new Csanquer\DebugTools\Dumper();
    $exception = new \ErrorException('an error occured', 101, 1, __FILE__, __LINE__, new \Exception('an exception'));
    return $dump->dump($exception, 'an exception', null, array('max_char' => null));
}

$res = testDumpException();
echo "<?php\n";
var_export($res);
echo ";\n";
//
//$res = $dump->backtrace(array('max_char' => null));
//var_export($res);