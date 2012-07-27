<?php

require 'myQueue.php';

$pdo = new PDO('mysql:host=127.0.0.1;dbname=queue;', 'root','');
$queue = new MyQueue($pdo, 'myqueue');
$queue->push('message1');
$queue->push('message2');
$queue->push('message3');

