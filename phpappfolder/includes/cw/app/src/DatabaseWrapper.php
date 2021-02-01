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
  global $settings;
  $db = createDBConn($settings['settings']['db']['host'], $settings['settings']['db']['user'], $settings['settings']['db']['pass'], $settings['settings']['db']['database']);
  updateDB($db);
  $msgs = $db->query("SELECT * FROM messages WHERE sourceMSISDN IS NOT NULL;");
  $r = Array();
  while ($row = $msgs->fetch_assoc()) {
    array_push($r, makeMessage($row));
  }
  return $r;
}

// like getMessages, but restricted to messages recieved between the specified times
function getMessagesIn(DateTime $from, DateTime $to) {
  global $settings;
  $db = createDBConn($settings['settings']['db']['host'], $settings['settings']['db']['user'], $settings['settings']['db']['pass'], $settings['settings']['db']['database']);
  updateDB($db);
  $statement = $db->prepare("SELECT * FROM messages where recievedTime >= STR_TO_DATE(?,\"%d/%m/%Y %H:%i:%s\") AND recievedTime <= STR_TO_DATE(?,\"%d/%m/%Y %H:%i:%s\") AND sourceMSISDN IS NOT NULL;");
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
// $query is the query string, res=maining aruments as parameters to bind_param()
// ie - first one is a string of 's' and 'i' indicating strings or numbers, rest are those strings or numbers
// directQuery('select * from table where x = ? and y = ?', 'is', 3, 'hello');
function directQuery(String $query, ...$args) {
  global $settings;
  $db = createDBConn($settings['settings']['db']['host'], $settings['settings']['db']['user'], $settings['settings']['db']['pass'], $settings['settings']['db']['database']);
  updateDB($db);
  $statement = $db->prepare($query);
  $statement->bind_param(...$args);
  $statement->execute();
  $msgs = $statement->get_result();
  $r = Array();
  while ($row = $msgs->fetch_assoc()) { // don't return "deleted" rows
    if (isset($row['sourceMSISDN'])) {
      array_push($r, makeMessage($row));
    }
  }
  return $r;
}

// takes a message id and deletes that message from the database.
// replaces all except id with NULL rather than truly deleting, this is because we're not deleting from the m2m server so it'd just get added again on the next updateDB()
function deleteMessage(String $id) {
  global $settings;
  $db = createDBConn($settings['settings']['db']['host'], $settings['settings']['db']['user'], $settings['settings']['db']['pass'], $settings['settings']['db']['database']);
  updateDB($db);
  $statement = $db->prepare("UPDATE messages SET sourceMSISDN = NULL, destinationMSISDN = NULL, recievedTime = NULL, bearer = NULL, messageRef = NULL, groupName = NULL, switch1 = NULL, switch2 = NULL, switch3 = NULL, switch4 = NULL, fan = NULL, heater = NULL, keypad = NULL WHERE id = ?;");
  $statement->bind_param('s',$id);
  $statement->execute();
  if ($statement) { // return whether successful
    return true;
  } else {
    return false;
  }
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
  global $settings;
  $soap = createSoapClient($settings['settings']['soap']['wsdl']);
  $msgs = $soap->peekMessages($settings['settings']['soap']['user'], $settings['settings']['soap']['pass'], 5); // last 5 messages
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
