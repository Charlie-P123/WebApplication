<?php

session_start();

require 'vendor/autoload.php';
$app_path = __DIR__ . '/app/';
require $app_path .'settings.php';
$container = new \Slim\Container($settings);
require $app_path .'dependancies.php';
$app = new \Slim\App($container);
require $app_path . 'routes.php';
$app->run();
