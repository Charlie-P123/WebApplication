<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessage', function(Request $request, Response $response) use ($app)
{

    $tainted_parameters = $request->getParsedBody();
    $cleaned_parameters = cleanParameters($app, $tainted_parameters);
    $message = createMessageString($cleaned_parameters);
    $send_message = performSendMessage($app, $message);


    return $this->view->render($response,
        'sendmessage.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APPLICATION_NAME,
            'page_heading_1' => APPLICATION_NAME,
            'page_heading_2' => 'Sent Message',
            'msg_string' => $message,
        ]);

})->setName('sendmessage');


function cleanParameters($app, array $tainted_parameters): array
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validateMsg');

    //validating switch values
    $tainted_switch_1 = $tainted_parameters['switch_1'];
    $tainted_switch_2 = $tainted_parameters['switch_2'];
    $tainted_switch_3 = $tainted_parameters['switch_3'];
    $tainted_switch_4 = $tainted_parameters['switch_4'];

    $tainted_fan_state = $tainted_parameters['fan_state'];
    $tainted_temp = $tainted_parameters['heater_temp'];
    $tainted_keypad = $tainted_parameters['keypad'];

    $cleaned_parameters['cleaned_switch_1'] = $validator->validateSwitch($tainted_switch_1);
    $cleaned_parameters['cleaned_switch_2'] = $validator->validateSwitch($tainted_switch_2);
    $cleaned_parameters['cleaned_switch_3'] = $validator->validateSwitch($tainted_switch_3);
    $cleaned_parameters['cleaned_switch_4'] = $validator->validateSwitch($tainted_switch_4);
    $cleaned_parameters['cleaned_fan'] = $validator->validateFan($tainted_fan_state);
    $cleaned_parameters['cleaned_temp'] = $validator->validateTemperature($tainted_temp);
    $cleaned_parameters['cleaned_keypad'] = $validator->validateNumberEntered($tainted_keypad);

    return $cleaned_parameters;

};

function createMessageString($cleaned_parameters): string
{
    $msg_string = '19_3110_BB ';
    $msg_string .= 'switch_1: ' . $cleaned_parameters['cleaned_switch_1'] . " ";
    $msg_string .= 'switch_2: ' . $cleaned_parameters['cleaned_switch_2'] . " ";
    $msg_string .= 'switch_3: ' . $cleaned_parameters['cleaned_switch_3'] . " ";
    $msg_string .= 'switch_4: ' . $cleaned_parameters['cleaned_switch_4'] . " ";
    $msg_string .= 'fan_state: ' . $cleaned_parameters['cleaned_fan'] . " ";
    $msg_string .= 'heater_temperature: ' . $cleaned_parameters['cleaned_temp'] . " ";
    $msg_string .= 'last_number_entered: ' . $cleaned_parameters['cleaned_keypad'];

    return $msg_string;
}
function performSendMessage($app, $validated_parameters)
{
    $soap_wrapper = $app->getContainer()->get('soapWrapper');
    $settings = $app->getContainer()->get('settings');
    $m2m_settings = $settings['m2m_connection'];

    $soap_client = $soap_wrapper->createSoapClient();

    $message = $validated_parameters;
    $username = $m2m_settings['username'];
    $password = $m2m_settings['password'];
    $msisdn = $m2m_settings['msisdn'];
    $deliver_report = $m2m_settings['delivery_report'];
    $mt_bearer = $m2m_settings['mt_bearer'];

    $webservice_call_result = $soap_client->sendMessage($username, $password, $msisdn, $message, $deliver_report, $mt_bearer);
    var_dump($webservice_call_result);
}