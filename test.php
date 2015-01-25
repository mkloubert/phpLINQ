<html>
  <head>
  </head>
  
  <body>
    <pre>
<?php

error_reporting(E_ALL);

require_once './System/Collections/Generic/IEnumerable.php';
require_once './System/Collections/Generic/EnumerableBase.php';
require_once './System/Linq/Enumerable.php';

use \System\Linq\Enumerable as Enumerable;

class TestClass {
	public function __toString() {
		return "Yep";
	}
}

$seq1 = Enumerable::fromValues(1, '2', 3, new TestClass());

$seq2 = $seq1->ofType('TestClass');
foreach ($seq2 as $i) {
	echo $i; ?><br /><?php
}

?>
    </pre>
  </body>
</html>