<?php

$use_xdebug = true;

if ($use_xdebug && function_exists(xdebug_start_trace()))
{
    ini_set('display_errors', 'On');
    ini_set('html_errors', 'On');
    ini_set('xdebug.trace_output_name', 'sessions.t%');
    ini_set('xdebug.trace_format', 1);

    xdebug_start_trace();
}

include_once 'session/bootstrap.php';

if ($use_xdebug && function_exists(xdebug_stop_trace()))
{
    xdebug_stop_trace();
}