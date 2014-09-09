<?php

include_once('./classes/Utilities.php');
include_once('./classes/BaseController.php');
include_once('./classes/BaseModel.php');
include_once('./classes/Loader.php');

$loader = new Loader();
$loader->LoadFromUrl();
$controller = $loader->CreateController();
$controller->ExecuteAction($loader->Action);
