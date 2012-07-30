<?php
class workersKill {
    /**
     * Construct the class
     */
    function killAllWorkers() {
        $this->listItems();
    }
    
    /**
     * List all the items
     */
    function listItems() {
        $output = shell_exec('ps -x | grep "php worker.php"');
        $workers =  explode("\n", $output);
        $this->doKill($workers);
    }
    
    /**
     * Kill all the processes
     * @param   array  $workers 
     */
    function doKill($workers) {
        /*
         *  PID TTY STAT TIME COMMAND
         */
        for ($i = 1; $i < count($workers); $i++) {
            $id =  substr($workers[$i], 0, strpos($workers[$i], ' t'));
            echo "Killing process with PID: ".$id;
            shell_exec('kill '.$id);
        }
    }
}

$killer = new workersKill();
$killer->killAllWorkers();
