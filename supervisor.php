<?php
require 'myQueue.php';
require 'config.php';

$pdo = new PDO('mysql:host='.HOST.';dbname='.DB_NAME.';', DB_USER, DB_PASSW);

while(1) {
  $result = $pdo->prepare('SELECT count(*) FROM myqueue');
  $result->execute(); 
  $number_of_rows = $result->fetchColumn();
  echo "Number of tasks waiting in the queue: ".$number_of_rows;
  echo "\r\n";
  sleep(5);
}

