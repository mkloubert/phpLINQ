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

use \System\Linq\Enumerable as Enumerable;

$seq = Enumerable::range(1, 10);

$list = $seq->toList();

?>
    </pre>
  </body>
</html>