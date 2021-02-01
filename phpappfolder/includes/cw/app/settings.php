<?php

$app_url = dirname($_SERVER['SCRIPT_NAME']);
$css_path = $app_url . '/css/sessions.css';
define('CSS_PATH', $css_path);


$settings = [
    "settings" => [
        'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'mode' => 'development',
    'debug' => true,
    'view' => [
        'template_path' => __DIR__ . '/templates/',
        'twig' => [
        'cahe' => false,
        'auto_reload' => true,
        ]],
    'db' => [
        'host' => 'localhost', // 'mysql.tech.dmu.ac.uk'
        'user' => 'p17170959_web',
        'pass' => 'fogGy~30',
        'database' => 'p17170959db'
        ],
    'soap' => [
        'wsdl' => 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl',
        'user' => '20_2420459',
        'pass' => 'Securewebapp123'
        ],

//    'pdo_settings' => [
//        'rdbms' => 'mysql',
//    'host' => 'LocalHost',
//        'db_name' => 'coursework_db',
//        'port' => '3306',
//        'user_name' => 'session_user_pass',
//        'charset' => 'utf8',
//        'collation' => 'utf8_unicode_ci',
//        'options' => [
//            PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
//            PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
//            PDO::ATTR_EMULATE_PREPARES     => true,
//        ],
    ]
];

