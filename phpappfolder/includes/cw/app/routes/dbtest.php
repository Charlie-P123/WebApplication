<?php

use Slim\Factory\AppFactory;

$app->get('/dbtest', function(Slim\Http\Request $request, Slim\Http\Response $response)
{$sid = session_id();

    return $this->view->render($response,
        'homepageform.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => $_SERVER["SCRIPT_NAME"],
            'action' => 'storesessiondetails',
            'initial_input_box_value' => null,
            'page_title' => 'demonstration',
            'page_heading_1' => 'Session Demonstration',
            'page_heading_2' => 'Enter values for storage in a session',
            'page_heading_3' => 'Select the type of session storage to be used',
            'info_text' => 'Your information will be stored in either a session file or in a database',
            'sid_text' => '',
            'sid' => print_r(getMessages(),false),
        ]);

}); //->setName('dbtest');
