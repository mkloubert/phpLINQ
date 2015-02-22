<html>
  <head>
  </head>
  
  <body>
    <pre>
<?php

error_reporting(E_ALL);

function __autoload($clsName) {
    require_once './' . str_replace('\\', '/', $clsName) . '.php';
}

use \System\Collections\Generic\Set;

$s = new Set();


?>
    </pre>
  </body>
</html>