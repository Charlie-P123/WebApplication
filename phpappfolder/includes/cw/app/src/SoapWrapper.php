<?php

namespace cw;

class SoapWrapper
{
    public function __construct(){}
    public function __destruct(){}

    public function constructSoap()
    {
        $soap_client_handle = false;
        $exception = '';
        $wsdl = WSDL;
        $soap_client_parameters = ['trace' => true, 'exceptions' => true];

        try{
            $soap_client_handle = new \SoapClient($wsdl, $soap_client_parameters);
            var_dump($soap_client_handle->__getFunctions());
            var_dump($soap_client_handle->__getTypes());
        }
        catch(\SoapFault $exception)
        {
            $soap_client_handle = 'something went wrong';
        }
        return $soap_client_handle;
    }
    public function performSoapCall($soap_client, $webservice_function, $webservice_call_parameters, $webservice_value)
    {
        $soap_call_result = null;
        $raw_xml = '';

        if ($soap_client)
        {
           try{
               $webservice_call_result = $soap_client->{$webservice_function}($webservice_call_parameters);
               $soap_call_result = $webservice_call_result->{$webservice_value};
           }
           catch (\SoapFault $exception){
               $soap_call_result = $exception;
           }

        }
        var_dump($soap_call_result);
        return $soap_call_result;

    }
}