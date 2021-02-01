<?php

class Message {
  // metadata
  public $id;
  public $sourceMSISDN;
  public $destinationMSISDN;
  public $recievedTime;
  public $bearer;
  public $messageRef;
  // content
  public $group;
  public $switch1;
  public $switch2;
  public $switch3;
  public $switch4;
  public $fan;
  public $heater;
  public $keypad;

  // initialiser
  function __construct($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n) {
    $this->id = $a;
    $this->sourceMSISDN = $b;
    $this->destinationMSISDN = $c;
    $this->recievedTime = $d;
    $this->bearer = $e;
    $this->messageRef = $f;
    $this->group = $g;
    $this->switch1 = $h;
    $this->switch2 = $i;
    $this->switch3 = $j;
    $this->switch4 = $k;
    $this->fan = $l;
    $this->heater = $m;
    $this->keypad = $n;
  }
}

// returns an array of Message objects, representing all the messages in the database
// guarantees
//   a) all messages added to the server since the last time a message was retrieved are in the database (ie. deleted messages won't be readded)
//   b) array is in order from oldest to newest message
function getMessages() {
  $db = createDBConn("mysql.tech.dmu.ac.uk","p17170959_web","fogGy~30","p17170959db");
  updateDB($db);
  $msgs = $db->query("SELECT * FROM messages;");
  $r = Array();
  while ($row = $msgs->fetch_assoc()) {
    array_push($r, makeMessage($row));
  }
  return $r;
}

// like getMessages, but restricted to messages recieved between the specified times
function getMessagesIn(DateTime $from, DateTime $to) {
  $db = createDBConn("mysql.tech.dmu.ac.uk","p17170959_web","fogGy~30","p17170959db");
  updateDB($db);
  $statement = $db->prepare("SELECT * FROM messages where recievedTime >= STR_TO_DATE(?,\"%d/%m/%Y %H:%i:%s\") AND recievedTime <= STR_TO_DATE(?,\"%d/%m/%Y %H:%i:%s\");");
  $statement->bind_param("ss",$from,$to);
  $from = $from->format("d/m/Y H:i:s");
  $to = $to->format("d/m/Y H:i:s");
  $statement->execute();
  $msgs = $statement->get_result();
  $r = Array();
  while ($row = $msgs->fetch_assoc()) {
    array_push($r, makeMessage($row));
  }
  return $r;
}

// like getMessages, but restricted to messages that are returned by the given SQL query.
// order is no longer order of age, but whatever order the query returns.
function directQuery(String $query, $args) {
  return Array(new Message());
}

// takes a message id and deletes that message from the database.
function deleteMessage(Int $id) {
  return true;
}

function createSoapClient($wsdl) {
  $soap_client_handle = false;
  $soapclient_attributes = ['trace' => true, 'exceptions' => true];
  try {
    $soap_client_handle = new SoapClient($wsdl, $soapclient_attributes);
  }
  catch (\SoapFault $exception) {
    trigger_error($exception);
  }
  return $soap_client_handle;
}

function createDBConn($server, $username, $password, $db) {
  $conn = new mysqli($server, $username, $password, $db);
  if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
  }
  return $conn;
}

function updateDB($db) {
  $soap = createSoapClient("https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl");
  $msgs = $soap->peekMessages('20_2420459','Securewebapp123',5); // hardcoded credentials not a good look
  $statement = $db->prepare("INSERT INTO messages (id, sourceMSISDN, destinationMSISDN, recievedTime, bearer, messageRef, groupName, switch1, switch2, switch3, switch4, fan, heater, keypad) VALUES (?,?,?,STR_TO_DATE(?,\"%d/%m/%Y %H:%i:%s\"),?,?,?,?,?,?,?,?,?,?);");
  if (!$statement) {
    die("could not prepare statement to update database: " . print_r($db,true));
  }
  $statement->bind_param("sssssisiiiisii",$id,$srcISDN,$destISDN,$recvTime,$bearer,$msgRef,$group,$switch1,$switch2,$switch3,$switch4,$fan,$heater,$keypad);
  foreach(parseMessages($msgs) as $msg) {
    // metadata
    $srcISDN = $msg->sourcemsisdn;
    $destISDN = $msg->destinationmsisdn;
    $recvTime = $msg->receivedtime;
    $bearer = $msg->bearer;
    $msgRef = (int)$msg->messageref;
    // content
    $group = $msg->message->group;
    $switch1 = $msg->message->switch1;
    $switch2 = $msg->message->switch2;
    $switch3 = $msg->message->switch3;
    $switch4 = $msg->message->switch4;
    $fan = $msg->message->fan;
    $heater = $msg->message->heater;
    $keypad = $msg->message->keypad;
    // have a unique identifier but don't insert duplicates
    $id = hash("md5", $srcISDN . $destISDN . $recvTime . $bearer . $msgRef . $group . $switch1 . $switch2 . $switch3 . $switch4 . $fan . $heater . $keypad);
    $statement->execute();
  }
}

function parseMessages($msgs) {
  $r = Array();
  foreach ($msgs as $msg) {
    try {
      $parsed = @simplexml_load_string($msgs[0]);
    } catch (exception $e) {
      $parsed = false;
    }
    if ($parsed) {
      $r[] = $parsed;
    }
  }
  return $r;
}

function makeMessage($sql_result) {
  return new Message($sql_result['id'],$sql_result['sourceMSISDN'],$sql_result['destinationMSISDN'],$sql_result['recievedTime'],$sql_result['bearer'],$sql_result['messageRef'],
                     $sql_result['groupName'],$sql_result['switch1'],$sql_result['switch2'],$sql_result['switch3'],$sql_result['switch4'],$sql_result['fan'],$sql_result['heater'],$sql_result['keypad']);
}
