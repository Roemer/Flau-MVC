<?php

include_once('./libs/FlauMVC.inc.php');

$loader = new FlauMVC\Loader();
$loader->loadFromUrl();
$controller = $loader->createController();
$controller->executeAction($loader->Action);
