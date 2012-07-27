<?php
require 'myQueue.php';
$pdo = new PDO('mysql:host=127.0.0.1;dbname=queue;', 'root','');
$queue = new MyQueue($pdo, 'myqueue');

while(1) {
  $msg = $queue->pop();
  if ($msg) {
    echo $msg;
  }
}
