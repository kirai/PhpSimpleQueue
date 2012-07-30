<?php

require 'myQueue.php';
require 'config.php';

$pdo = new PDO('mysql:host='.HOST.';dbname='.DB_NAME.';', DB_USER, DB_PASSW);
$queue = new MyQueue($pdo, 'myqueue');

while(1) {
  $queue->push('message1');
  $queue->push('message2');
  $queue->push('message3');
  sleep(1);
}

