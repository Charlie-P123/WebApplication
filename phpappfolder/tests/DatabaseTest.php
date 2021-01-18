<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    public function testConnectDatabase() {
        $f_dir = __DIR__. '\..\\';
        include_once($f_dir . 'src\DatabaseWrapper.php');

        $f_obj_mysql_wrapper = new \Coursework\DatabaseWrapper();
        $f_boolean_value = false;

        if($f_obj_mysql_wrapper -> connectToDatabase()) {
            $f_boolean_value = true;
        }
        $this -> assertEquals(true, $f_boolean_value);
    }

    public function testSafeQuery() {
        $f_dir = __DIR__. '\..\\';
        include_once($f_dir . 'src\DatabaseWrapper.php');
        include_once($f_dir . 'src\SQLQueries.php');

        $f_obj_wrapper_sql = new \Coursework\DatabaseWrapper();
        $f_obj_wrapper_db = new \Coursework\DatabaseWrapper();

        $f_obj_wrapper_db -> connectToDatabase();

        $f_boolean_value = false;
        if($f_obj_wrapper_db ->setsafeQuery($f_obj_wrapper_sql -> get_admin_id(), null)) {
            $f_boolean_value = true;
        }
        $this -> assertEquals(true, $f_boolean_value);
    }

    public function testCountRows() {
        $f_dir = __DIR__. '\..\\';
        include_once($f_dir . 'src\DatabaseWrapper.php');
        include_once($f_dir . 'src\SQLQueries.php');

        $f_obj_wrapper_sql = new \Coursework\DatabaseWrapper();
        $f_obj_wrapper_db = new \Coursework\DatabaseWrapper();

        $f_obj_wrapper_db -> connectToDatabase();
        $f_obj_wrapper_db ->setSafeQuery($f_obj_wrapper_sql -> get_admin_id(), null);
        $f_number_of_rows = $f_obj_wrapper_db ->countRows();

        $f_returned_rows = false;

        if($f_number_of_rows > 0) {
            $f_returned_rows = true;
        }
        $this -> assertEquals(true, $f_returned_rows);
    }

    public function testFetchAll() {
        $f_dir = __DIR__. '\..\\';
        include_once($f_dir . 'src\DatabaseWrapper.php');
        include_once($f_dir . 'src\SQLQueries.php');

        $f_obj_wrapper_sql = new \Coursework\DatabaseWrapper();
        $f_obj_wrapper_db = new \Coursework\DatabaseWrapper();

        $f_obj_wrapper_db -> connectToDatabase();
        $f_obj_wrapper_db ->setSafeQuery($f_obj_wrapper_sql -> get_admin_id(), null);
        $f_number_of_rows = $f_obj_wrapper_db ->safeFetchArray();

        $f_returned_rows = false;

        if($f_number_of_rows > 0) {
            $f_returned_rows = true;
        }
        $this -> assertEquals(true, $f_returned_rows);
    }
}