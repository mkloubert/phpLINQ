<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'Home';

?>
<ol class="breadcrumb">
  <li class="active">Home</li>
</ol>

<div class="panel panel-default">
  <div class="panel-heading">A</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_all.php" class="list-group-item">all()</a>
      <a href="examples_any.php" class="list-group-item">any()</a>
      <a href="examples_average.php" class="list-group-item">average()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">C</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_cast.php" class="list-group-item">cast()</a>
      <a href="examples_contains.php" class="list-group-item">contains()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">D</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_defaultIfEmpty.php" class="list-group-item">defaultIfEmpty()</a>
      <a href="examples_distinct.php" class="list-group-item">distinct()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">E</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_elementAtOrDefault.php" class="list-group-item">elementAtOrDefault()</a>
      <a href="examples_except.php" class="list-group-item">except()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">F</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_firstOrDefault.php" class="list-group-item">firstOrDefault()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">G</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_groupBy.php" class="list-group-item">groupBy()</a>
      <a href="examples_groupJoin.php" class="list-group-item">groupJoin()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">I</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_intersect.php" class="list-group-item">intersect()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">J</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_join.php" class="list-group-item">join()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">L</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_lastOrDefault.php" class="list-group-item">lastOrDefault()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">M</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_max.php" class="list-group-item">max()</a>
      <a href="examples_min.php" class="list-group-item">min()</a>
      <a href="examples_multiply.php" class="list-group-item">multiply()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">O</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_ofType.php" class="list-group-item">ofType()</a>
      <a href="examples_orderBy.php" class="list-group-item">orderBy()</a>
      <a href="examples_orderByDescending.php" class="list-group-item">orderByDescending()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">P</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_product.php" class="list-group-item">product()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">R</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_reverse.php" class="list-group-item">reverse()</a>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">S</div>
  
  <div class="panel-body">
    <div class="list-group">
      <a href="examples_singleOrDefault.php" class="list-group-item">singleOrDefault()</a>
      <a href="examples_sum.php" class="list-group-item">sum()</a>
    </div>
  </div>
</div>
<?php

require_once './shutdown.inc.php';
