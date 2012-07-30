PhpSimpleQueue
==============

Super simple queue messaging using PHP/MysqlInnoDB.
No multithreading, no nothing, just a fucking simple queue in a Mysql Table.


     Run "php enqueue.php" to enqueue messages

     Run "php worker.php" to consume messages

     Run "php supervisor.php" to see how many messages are waiting to be processed
