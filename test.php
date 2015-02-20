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

class Person {
    public function __construct($name) {
        $this->Name = $name;
    }

    public $Name;
}

class Pet {
    public function __construct($name, Person $owner) {
        $this->Name = $name;
        $this->Owner = $owner;
    }

    public $Name;
    public $Owner;
}

$persons = array(new Person("Tanja"),
                 new Person("Marcel"),
                 new Person("Yvonne"),
                 new Person("Josefine"));

$pets = array(new Pet("Gina"     , $persons[1]),
              new Pet("Schnuffi" , $persons[1]),
              new Pet("Schnuffel", $persons[2]),
              new Pet("WauWau"   , $persons[0]),
              new Pet("Lulu"     , $persons[3]),
              new Pet("Asta"     , $persons[1]));

$personSeq = Enumerable::fromArray($persons);
$petSeq    = Enumerable::fromArray($pets);

$joined = $personSeq->join($petSeq,
                           function($person) { return $person->Name; },
                           function($pet) { return $pet->Owner->Name; },
                           function($person, $pet) {
                               return sprintf('Owner: %s; Pet: %s',
                                              $person->Name,
                                              $pet->Name);
                           });

foreach ($joined as $item) {
    echo htmlentities($item) . '<br />';
}


?>
    </pre>
  </body>
</html>