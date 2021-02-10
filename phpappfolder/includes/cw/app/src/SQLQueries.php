<?php

namespace cw;

class SQLQueries
{
    public function __construct() {}
    public function __destruct() {}

    public function getMessageData()
    {
        $query_string = "SELECT board_id, msisdn, name, date, switch1, switch2,";
        $query_string .= " switch3, switch4, fan, temperature, keypad";
        $query_string .= " FROM board_status";
        return $query_string;
    }
    public function insertMessageData()
    {
        $query_string = "INSERT INTO board_status";
        $query_string .= " SET ";
        $query_string .= "msisdn = :msisdn, ";
        $query_string .= "name = :team_name, ";
        $query_string .= "date = :timestamp, ";
        $query_string .= "switch1 = :switch1, ";
        $query_string .= "switch2 = :switch2, ";
        $query_string .= "switch3 = :switch3, ";
        $query_string .= "switch4 = :switch4, ";
        $query_string .= "fan = :fan_state, ";
        $query_string .= "temperature = :heater_temp, ";
        $query_string .= "keypad = :keypad";

        return $query_string;
    }
}