<?php

session_start();

require 'vendor/auroload.php';
$app_path = __DIR__ . '/app/';
$settings = require $app_path .'settings.php';
$container = new \Slim\Container($settings);
require $app_path .'dependencies.php';
$app = new \Slim\App($container);
require $app_path . 'routs.php';
$app->run();
