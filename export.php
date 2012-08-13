<?php
array (
  'type' => 'exception',
  'composite' => true,
  'class' => 'ErrorException',
  'properties' => 
  array (
    'message' => 
    array (
      'name' => 'message',
      'type' => 'property',
      'composite' => false,
      'access' => 'protected',
      'static' => false,
      'value' => 
      array (
        'type' => 'string',
        'value' => 'an error occured',
        'length' => 16,
        'max_length' => NULL,
        'composite' => false,
      ),
    ),
    'code' => 
    array (
      'name' => 'code',
      'type' => 'property',
      'composite' => false,
      'access' => 'protected',
      'static' => false,
      'value' => 
      array (
        'type' => 'int',
        'value' => 101,
        'composite' => false,
      ),
    ),
    'file' => 
    array (
      'name' => 'file',
      'type' => 'property',
      'composite' => false,
      'access' => 'protected',
      'static' => false,
      'value' => 
      array (
        'type' => 'string',
        'value' => '/home/charles/git/DebugTools/test.php',
        'length' => 37,
        'max_length' => NULL,
        'composite' => false,
      ),
    ),
    'line' => 
    array (
      'name' => 'line',
      'type' => 'property',
      'composite' => false,
      'access' => 'protected',
      'static' => false,
      'value' => 
      array (
        'type' => 'int',
        'value' => 9,
        'composite' => false,
      ),
    ),
    'severity' => 
    array (
      'name' => 'severity',
      'type' => 'property',
      'composite' => false,
      'access' => 'protected',
      'static' => false,
      'value' => 
      array (
        'type' => 'int',
        'value' => 1,
        'composite' => false,
      ),
    ),
    'trace' => 
    array (
      'type' => 'backtrace',
      'composite' => true,
      'max_char' => 180,
      'value' => 
      array (
        0 => 
        array (
          'function' => 'testDumpException',
          'line' => 13,
          'file' => '/home/charles/git/DebugTools/test.php',
        ),
      ),
    ),
    'previous' => 
    array (
      'type' => 'exception',
      'composite' => true,
      'class' => 'Exception',
      'properties' => 
      array (
        'message' => 
        array (
          'name' => 'message',
          'type' => 'property',
          'composite' => false,
          'access' => 'protected',
          'static' => false,
          'value' => 
          array (
            'type' => 'string',
            'value' => 'an exception',
            'length' => 12,
            'max_length' => NULL,
            'composite' => false,
          ),
        ),
        'string' => 
        array (
          'name' => 'string',
          'type' => 'property',
          'composite' => false,
          'access' => 'private',
          'static' => false,
          'value' => 
          array (
            'type' => 'string',
            'value' => '',
            'length' => 0,
            'max_length' => NULL,
            'composite' => false,
          ),
        ),
        'code' => 
        array (
          'name' => 'code',
          'type' => 'property',
          'composite' => false,
          'access' => 'protected',
          'static' => false,
          'value' => 
          array (
            'type' => 'int',
            'value' => 0,
            'composite' => false,
          ),
        ),
        'file' => 
        array (
          'name' => 'file',
          'type' => 'property',
          'composite' => false,
          'access' => 'protected',
          'static' => false,
          'value' => 
          array (
            'type' => 'string',
            'value' => '/home/charles/git/DebugTools/test.php',
            'length' => 37,
            'max_length' => NULL,
            'composite' => false,
          ),
        ),
        'line' => 
        array (
          'name' => 'line',
          'type' => 'property',
          'composite' => false,
          'access' => 'protected',
          'static' => false,
          'value' => 
          array (
            'type' => 'int',
            'value' => 9,
            'composite' => false,
          ),
        ),
      ),
    ),
  ),
  'name' => 'an exception',
  'call' => 
  array (
    'file' => '/home/charles/git/DebugTools/test.php',
    'line' => 10,
  ),
);
