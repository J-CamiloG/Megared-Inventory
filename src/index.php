<?php
session_start();
define('BASE_PATH', __DIR__);

require_once 'config/config.php';
require_once 'autoload.php';
require_once 'core/Router.php';

$router = new Router();
$router->dispatch();
