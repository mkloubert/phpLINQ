<?php

require_once './bootstrap.inc.php';


$result = array();

register_shutdown_function(function() use (&$result) {
                               ob_end_clean();

                               header('Content-type: application/json; charset: utf-8');
                               echo json_encode($result);
                           });

set_error_handler(function($errNr, $errMsg, $errFile, $errLine) use (&$result) {
                      $result['code'] = -2;
                      $result['msg']  = 'Error';
                      $result['data'] = array(
                          'code' => $errNr,
                          'msg'  => $errMsg,
                          'file' => array(
                              'name' => $errFile,
                              'line' => $errLine,
                          )
                      );
                  }, E_ALL);

set_exception_handler(function($ex) use (&$result) {
                          $result['code'] = -1;
                          $result['msg']  = 'Exception';
                          $result['data'] = array(
                              'code' => $ex->getCode(),
                              'msg'  => $ex->getMessage(),
                              'file' => array(
                                  'name' => $ex->getFile(),
                                  'line' => $ex->getLine(),
                              )
                          );
                      });


// execute code
$start = microtime(true);
eval($_POST['code']);
$end = microtime(true);


$result['code'] = 0;
$result['data'] = array(
    'content' => ob_get_contents(),
    'duration' => $end - $start,
);
$result['msg']  = 'OK';

