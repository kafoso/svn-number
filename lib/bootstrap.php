<?php
define("BASE_DIR", readlink(dirname(__FILE__)));

$loader = require(BASE_DIR . "/vendor/autoload.php");
$loader->addClassMap(require(BASE_DIR . "/autoload_classmap.php"));
