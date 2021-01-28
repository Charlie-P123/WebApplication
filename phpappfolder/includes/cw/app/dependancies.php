<?php
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig'],
        [
            'debug' => true // This line should enable debug mode
        ]
    );

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};


$container['sessionValidator'] = function () {
    $validator = new SessionValidator();
    return $validator;
};

    $container['sessionWrapper'] = function () {
        $session_wrapper = new SessionWrapper();
        return $session_wrapper;
};

    $container['mysqlWrapper'] = function () {
        $mysql_wrapper = new \Cw\DatabaseWrapper();
        return $mysql_wrapper;
};

    $container['sessionModel'] = function ($container) {
        $session_model = new \Cw\SessionModel();
        return $session_model;
};

    $container['sqlQueries'] = function () {
        $sql_queries = new \Cw\SQLQueries();
        return $sql_queries;
    };

    $container['databaseWrapper'] = function ($container) {
        $database_wrapper_handle = new \Cw\DatabaseWrapper();
        return $database_wrapper_handle;
    };

    $container['loggerWrapper'] = function ($container) {
        $logging_wrapper = new \Monolog\logger('logger');
        return $logging_wrapper;
    };

require $app_path . 'src/DatabaseWrapper.php';
