<?php

$a = array("a", "b", "c");

foreach ($a as &$v) {};  // aliasing on $v
foreach ($a as $v) {};

$a[2] = "x";

var_dump($v);

?>
