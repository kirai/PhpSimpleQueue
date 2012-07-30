<?php
class MyQueue
{
  /**
   * Seconds a message lock will expire in.
   */
  const LOCK_TIMEOUT = 10;

  /**
   * @var PDO The PDO instance for accessing MySQL.
   */
  protected $pdo;

  /**
   * @var string The name of the queue table on the database.
   */
  protected $qname;

  /**
   * @param  PDO     The PDO instance for accessing MySQL.
   * @param  string  The name of the queue table on the database.
   */
  public function __construct($pdo, $qname) {
    $this->pdo = $pdo;
    $this->qname = $qname;
  }

  /**
   * Push a message to the end of the queue.
   *
   * @param  string  The data of the message to be pushed.
   * @return bool    true on success, false on failure.
   */
  public function push($data) {
    $sth = $this->pdo->prepare("INSERT INTO `{$this->qname}` SET data = :data;");
    $sth->bindParam(':data', $data);
    return $sth->execute();
  }

  /**
   * Pop a message from the top of the queue.
   * By calling this method, the message in the top of the queue will be
   * removed from the queue.
   *
   * @return string|bool
   *    The message popped out from the queue on success.
   *    false on failure to pop a message or empty queue.
   */
  public function pop() {
    // Lock the top message in the queue.
    // The lock will expire in self::LOCK_TIMEOUT.
    $affected = $this->pdo->exec("
      UPDATE `{$this->qname}`
        SET id = LAST_INSERT_ID(id),
            locked_until = NOW() + INTERVAL ".self::LOCK_TIMEOUT." SECOND
        WHERE locked_until < NOW() ORDER BY id LIMIT 1;
    ");

    // The queue is empty
    if ($affected == 0) {
      return FALSE;
    }

    //Get the ID of the locked message.
    $msg_id = $this->pdo->lastInsertId();
    if (!$msg_id) {
      // No message in the queue, or failed to lock a message
      return false;
    }

    // Get the data of the locked message.
    $sth_select = $this->pdo->prepare("
      SELECT data FROM `{$this->qname}` WHERE id = :msg_id;
    ");
    $sth_select->bindParam(':msg_id', $msg_id, PDO::PARAM_INT);
    
    if (!$sth_select->execute()) {
      // In this case, we locked the message but have failed to get the data.
      // The message will stay in the queue for 10 secs, 
      // another client will process it.
      return false;
    }
    $msg_data = $sth_select->fetchColumn();

    // Do the task


    // Delete the locked message from the queue.
    $sth_delete = $this->pdo->prepare("
      DELETE FROM `{$this->qname}` WHERE id = :msg_id;
    ");
    $sth_delete->bindParam(':msg_id', $msg_id, PDO::PARAM_INT);
    if (!$sth_delete->execute()) {
      // In this case, we have the message data but the message stays in the queue,
      // so here we ignore the message data so that another client will receive it.
      return false;
    }

    return $msg_data;
  }

}

