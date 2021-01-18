<?php

require 'vendor/autoload.php';

$app_path = __DIR__ . '/app/';

$setting = require $app_path . 'settings.php';

$container = new \Slim\Container($setting);

require $app_path . 'dependencies.php';

$app = new \Slim\App($container);

require $app_path . 'routes.php';

$app->run();