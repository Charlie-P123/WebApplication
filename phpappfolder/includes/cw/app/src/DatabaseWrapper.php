<?php

class Message {
  public $id;
  public $sourceMSISDN;
  public $destinationMSISDN;
  public $recievedTime;
  public $bearer;
  public $messageRef;
  public $messageContent;
}

// create clients once
//$soap = createSoapClient("https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl");
//$db = createDBConn("localhost","");

// returns an array of Message objects, representing all the messages in the database
// guarantees
//   a) all messages added to the server since the last time a message was retrieved are in the database (ie. deleted messages won't be readded)
//   b) array is in order from oldest to newest message
function getMessages() {
  return Array(new Message());
}

// like getMessages, but restricted to messages recieved between the specified times
function getMessagesIn(DateTime $from, DateTime $to) {
  return Array(new Message());
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

function updateDB() {
  $msgs = $soap->peekMessages('20_2420459','Securewebapp123',5); // hardcoded credentials not a good look
}
