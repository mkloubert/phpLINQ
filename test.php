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

class TestClass {
    public function __toString() {
        return "Yep";
    }
}

$seq1 = Enumerable::range(0, 100);

$seq2 = $seq1->groupBy(function($item) {
                           switch($item % 3) {
                               case 1:
                                   return 'ONE';
                                   
                               case 2:
                                      return 'twO';
                           }
                           
                           return 'Zero';
                        });

foreach ($seq2 as $grp) {
    echo htmlentities($grp->key()); ?><br /><?php

    foreach ($grp as $i) {
        ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
        echo htmlentities($i); ?><br /><?php
    }
}

?>
    </pre>
  </body>
</html>