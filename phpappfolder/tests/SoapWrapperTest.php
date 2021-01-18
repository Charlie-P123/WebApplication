<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SoapWrapperTest extends TestCase
{
    protected $c_wsdl = 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl';

    public function testSoapConnection() {
        $f_dir = __DIR__ . '\..\\';
        include_once($f_dir . 'src\SoapWrapper.php');

        $f_obj_soap = new \Coursework\SoapWrapper();
        $f_obj_soap -> set_wsdl($this -> c_wsdl);
        $this ->assertFalse($f_obj_soap -> make_soap_client());
    }

    public function testSoapConnectionError() {
        $f_dir = __DIR__ . '\..\\';
        include_once($f_dir . 'src\SoapWrapper.php');

        $f_obj_soap = new \Coursework\SoapWrapper();
        $f_obj_soap -> set_wsdl(null);
        $this -> assertTrue($f_obj_soap -> make_soap_client());
    }
}
