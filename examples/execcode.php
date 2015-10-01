<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

require_once './bootstrap.inc.php';


$result = [];


register_shutdown_function(function() use (&$result) {
                               ob_end_clean();

                               header('Content-type: application/json; charset: utf-8');
                               echo json_encode($result);
                           });

set_error_handler(function($errNr, $errMsg, $errFile, $errLine) use (&$result) {
                      $result['code'] = -2;
                      $result['msg']  = 'Error';
                      $result['data'] = [
                          'code' => $errNr,
                          'msg'  => $errMsg,
                          'file' => [
                              'name' => $errFile,
                              'line' => $errLine,
                          ]
                      ];
                  }, E_ALL);

set_exception_handler(function($ex) use (&$result) {
                          $result['code'] = -1;
                          $result['msg']  = 'Exception';
                          $result['data'] = [
                              'code' => $ex->getCode(),
                              'msg'  => $ex->getMessage(),
                              'file' => [
                                  'name' => $ex->getFile(),
                                  'line' => $ex->getLine(),
                              ]
                          ];
                      });


function getMemoryLimit() : int {
    $limit = trim(ini_get('memory_limit'));

    if (1 === preg_match('/^(\d+)(.)$/', $limit, $matches)) {
        if (isset($matches[2])) {
            switch (trim(strtoupper($matches[2]))) {
                case 'K':
                    $limit = (int)$matches[1] * 1024;
                    break;

                case 'M':
                    $limit = (int)$matches[1] * 1024 * 1024;
                    break;
            }
        }
    }

    return (int)$limit;
}


// execute code
$start = microtime(true);

$memoryBefore = memory_get_usage();
eval($_POST['code']);

$end = microtime(true);

$memoryAfter = memory_get_usage();

$result['code'] = 0;
$result['data'] = [
    'content' => ob_get_contents(),
    'time' => [
        'start' => $start,
        'end' => $end,
        'duration' => $end - $start,
    ],
    'memory' => [
        'allocated' => [
            'before' => $memoryBefore,
            'after' => $memoryAfter,
            'difference' => $memoryAfter - $memoryBefore,
        ],
        'limit' => getMemoryLimit(),
    ],
];
$result['msg']  = 'OK';
