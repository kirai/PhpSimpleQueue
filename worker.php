<?php
require 'myQueue.php';
require 'config.php';

$pdo = new PDO('mysql:host='.HOST.';dbname='.DB_NAME.';', DB_USER, DB_PASSW);

$queue = new MyQueue($pdo, 'myqueue');

while(1) {
  $msg = $queue->pop();
  if ($msg) {
    echo $msg;
    echo "\r\n";
  }
}
