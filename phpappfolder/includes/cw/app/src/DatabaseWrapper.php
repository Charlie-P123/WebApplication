<?php


class Message {}

function getMessages() {
  return Array(new Message);
}
function getMessagesIn(DateTime $from, DateTime $to) {
  return Array(new Message);
}
function directQuery(String $query, $args) {
  return Array(new Message);
}
