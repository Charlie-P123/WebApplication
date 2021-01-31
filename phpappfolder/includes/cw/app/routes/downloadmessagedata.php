<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->post('/downloadmessagedata', function (Request $request, Response $response) use ($app) {

    $message = getTableData($app);

    return $this->view->render($response,
        'downloadmessagedata.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APPLICATION_NAME,
            'page_heading_1' => APPLICATION_NAME,
            'page_heading_2' => 'Downloaded Message Data',
            'table' => $message,
        ]);
})->setName('downloadmessagedata');

function getTableData($app)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('sqlQueries');
    $model_wrapper = $app->getContainer()->get('messageDataModel');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['pdo_settings'];

    $model_wrapper->setDatabaseConnectionSettings($database_connection_settings);
    $model_wrapper->setDatabaseWrapper($database_wrapper);
    $model_wrapper->setSqlQueries($sql_queries);
    $message = $model_wrapper->getMessage();

    var_dump($message);
}