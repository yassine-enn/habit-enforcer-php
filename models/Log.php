<?php

class Log{
    public $id ;
    public $task_id;
    public $created_at;
    public $score;

    function __construct($task_id){
        $this->task_id = $task_id;
        $this->score = Task::getById($this->task_id)->taskScore();
    }

    /*
    
    get task last enterie from log table


    ///////
    
    if last entery < today entery then add new entery to log table


    //////

    get group tasks and for each task get last log entery


    */

    // get task last enterie from log table
    static function getLastTaskEntery($task_id){
        $sql = "SELECT * FROM log WHERE task_id = $task_id ORDER BY created_at DESC";
        $myCon = ConfigClass::getConnection();
        $result = $myCon->query($sql);
        $lastEntery = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $lastEntery[] = $row;
            }
        }
        return $lastEntery;
    }

    static function taskLogCount($task_id){
        $sql = "SELECT count(id) as count FROM log WHERE task_id = $task_id";
        $myCon = ConfigClass::getConnection();
        $result = $myCon->query($sql);
        
        $count = 0;

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $count = $row["count"];
            }
        }
        return $count;
    }

        
    function addNewTask(){
        $lastTask = Log::getLastTaskEntery($this->task_id)[0];
        
        if(!empty($lastTask)){
            echo "<br>last entery is not empty";
            echo $lastTask["task_id"];
            $currentScore = Task::getById($lastTask["task_id"])->taskScore();
            $lastScore = $lastTask["score"];

            if($currentScore < $lastScore){
                echo "<br>current score is less than last score";
                $log = new Log($this->task_id, $this->score);
                $log->save();
            }
        }
        else{
            echo "<br>Task not found and added";
            $log = new Log($this->task_id, $this->score);
            $log->save();
        }
    }


    public function save(){
        $sql = "INSERT INTO log (task_id, score) VALUES ($this->task_id, $this->score)";
        $myCon = ConfigClass::getConnection();
        $result = $myCon->query($sql);
        return $result;
    }


}
