<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
$app->get('/viewmessagedata', function(Request $request, Response $response) use ($app)
{
    $view_message = getMessageData($app);
    $get_tainted_meta_data = getMetaData($app, $view_message);
    $cleaned_meta_data = cleanMetaData($app, $get_tainted_meta_data);
    $store_message = storeDownloadedMessages($app, $cleaned_meta_data);

    return $this->view->render($response,
        'viewmessage.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APPLICATION_NAME,
            'page_heading_1' => APPLICATION_NAME,
            'page_heading_2' => 'View Messages Sent',
            'action' => 'downloadmessagedata',
            'method' => 'post',
            'data' => $view_message,
        ]);
})->setName('viewmessagedata');

function getMessageData($app)
{
    $soap_wrapper = $app->getContainer()->get('soapWrapper');
    $settings = $app->getContainer()->get('settings');
    $m2m_settings = $settings['m2m_connection'];

    $soap_client = $soap_wrapper->createSoapClient();

    $username = $m2m_settings['username'];
    $password = $m2m_settings['password'];
    $count = 100;
    $msisdn = $m2m_settings['msisdn'];
    $country_code = "+44";

    $webservice_call_result = $soap_client->peekMessages($username, $password, $count);
    $filtered_result = array();

    foreach ($webservice_call_result as $position => $team_name)
    {
        if (strpos($team_name, '19_3110_BB') == true)
        {
            $filtered_result[$position] = $team_name;
        }
    }

    return $filtered_result;
}

function getMetaData($app, array $message_data)
{
    $meta_data = array();
    $blacklist_words = ['switch_1:', 'switch_2:', 'switch_3:', 'switch_4:', 'fan_state:', 'heater_temperature:', 'last_number_entered:'];
    $message_array = array();

    foreach($message_data as $position => $data)
    {
        $string_data = $data;
        $xml = simplexml_load_string($data);
        $data_in_tag[$position]['msisdn'] = (string) $xml->sourcemsisdn;
        $data_in_tag[$position]['received_time'] = (string) $xml->receivedtime;
        $message_string = (string) $xml->message;

        $message_array[$position] = explode(' ', $message_string);

        foreach ($message_array[$position] as $key => $value){
            if (in_array($value, $blacklist_words))
            {
                unset($message_array[$position][$key]);
            }
        }

        foreach ($message_array[$position] as $key => $value)
        {
            switch ($key){
                case 0:
                    $data_in_tag[$position]['team_name'] = $value;
                    break;
                case 2:
                    $data_in_tag[$position]['switch_1'] = $value;
                    break;
                case 4:
                    $data_in_tag[$position]['switch_2'] = $value;
                    break;
                case 6:
                    $data_in_tag[$position]['switch_3'] = $value;
                    break;
                case 8:
                    $data_in_tag[$position]['switch_4'] = $value;
                    break;
                case 10:
                    $data_in_tag[$position]['fan_state'] = $value;
                    break;
                case 12:
                    $data_in_tag[$position]['heater_temperature'] = $value;
                    break;
                case 14:
                    $data_in_tag[$position]['keypad'] = $value;
                    break;
            }
        }
        $meta_data = $data_in_tag;
    }

    return $meta_data;
}

function cleanMetaData($app, $tainted_data): array
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('validateMsg');

    foreach($tainted_data as $position => $data)
    {
        $cleaned_parameters[$position]['team_name'] = $validator->validateMetaData($tainted_data[$position]['team_name']);
        $cleaned_parameters[$position]['msisdn'] = $validator->validateMsisdn($tainted_data[$position]['msisdn']);
        $cleaned_parameters[$position]['received_time'] = $tainted_data[$position]['received_time'];
        $cleaned_parameters[$position]['switch_1'] = $validator->validateSwitch($tainted_data[$position]['switch_1']);
        $cleaned_parameters[$position]['switch_2'] = $validator->validateSwitch($tainted_data[$position]['switch_2']);
        $cleaned_parameters[$position]['switch_3'] = $validator->validateSwitch($tainted_data[$position]['switch_3']);
        $cleaned_parameters[$position]['switch_4'] = $validator->validateSwitch($tainted_data[$position]['switch_4']);
        $cleaned_parameters[$position]['fan_state'] = $validator->validateFan($tainted_data[$position]['fan_state']);
        $cleaned_parameters[$position]['heater_temperature'] = $validator->validateTemperature($tainted_data[$position]['heater_temperature']);
        $cleaned_parameters[$position]['keypad'] = $validator->validateNumberEntered($tainted_data[$position]['keypad']);
    }

    return $cleaned_parameters;
}

function storeDownloadedMessages($app, $cleaned_parameters)
{
    $database_wrapper = $app->getContainer()->get('databaseWrapper');
    $sql_queries = $app->getContainer()->get('sqlQueries');
    $model_wrapper = $app->getContainer()->get('messageDataModel');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['pdo_settings'];

    $model_wrapper->setDatabaseConnectionSettings($database_connection_settings);
    $model_wrapper->setDatabaseWrapper($database_wrapper);
    $model_wrapper->setSqlQueries($sql_queries);

    foreach ($cleaned_parameters as $position => $data)
    {
        $model_wrapper->storeMessage($cleaned_parameters[$position]);
    }
}