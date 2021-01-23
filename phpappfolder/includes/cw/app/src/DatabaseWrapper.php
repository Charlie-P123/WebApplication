<?php

// I don't know what a message consists of yet, but they definitely have an id.
class Message { public $id = 0; }

// returns an array of Message objects, representing all the messages in the database
// guarantees
//   a) all messages added to the server since the last time a message was retrieved are in the database (ie. deleted messages won't be readded)
//   b) array is in order from oldest to newest message
function getMessages() {
  return Array(new Message(0));
}

// like getMessages, but restricted to messages recieved between the specified times
function getMessagesIn(DateTime $from, DateTime $to) {
  return Array(new Message(0));
}

// like getMessages, but restricted to messages that are returned by the given SQL query.
// order is no longer order of age, but whatever order the query returns.
function directQuery(String $query, $args) {
  return Array(new Message(0));
}

// takes a message id and deletes that message from the database.
function deleteMessage(Int $id) {
  return true;
}
