<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/createmessage', function (Request $request, Response $response) use ($app) {

    $page_text = 'Select the state of the circuit board to send in the message';

    return $this->view->render($response,
        'createmessageform.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'post',
            'action' => 'sendmessage',
            'page_title' => APPLICATION_NAME,
            'page_heading_1' => APPLICATION_NAME,
            'page_heading_2' => 'Enter message details',
            'page_text' => $page_text,
        ]);
})->setName('createmessage');