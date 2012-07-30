<?php

//Takes care of having enough workers running
require 'myQueue.php';
require 'config.php';
require 'workerskill.php';

$pdo = new PDO('mysql:host='.HOST.';dbname='.DB_NAME.';', DB_USER, DB_PASSW);

$killer = new workersKill();

function isRunning($pid){
    try{
        $result = shell_exec(sprintf("ps %d", $pid));
        if( count(preg_split("/\n/", $result)) > 2){
            return true;
        }
    }catch(Exception $e){}

    return false;
}

$i = 0;
while(1) {
  $result = $pdo->prepare('SELECT count(*) FROM myqueue');
  $result->execute(); 
  $number_of_rows = $result->fetchColumn();

  if ($number_of_rows > LAUNCH_NEW_WORKER_IF_QUEUE_OVER) {
      echo "Launching a new worker ";
      echo "\r\n";
      
      $cmd = "php worker.php";
      $outputfile = "worker".$i;
      $pidfile = 0;

      exec(sprintf("%s > /dev/null 2>&1 & echo $! >> %s", $cmd, $pidfile));

      echo "\r\n";
  
      $i++;
  }
  
  if ($number_of_rows < 3) {
    $killer->killAllWorkers();
  } 

  echo "Number of tasks waiting in the queue: ".$number_of_rows;
  echo "\r\n";
  sleep(5);
}



