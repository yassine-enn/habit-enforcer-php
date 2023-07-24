<?php
require_once "Task.php"; 
class Check 
{
    public $id;
    public $group_id;
    public $task_id;
    public $created_at;
    public $user_id;

    public function __construct()
    {
        $this->user_id = $_SESSION["id"];
    }

    public function save() 
    {
        if ($this->isItIn() && $this->isUsersTask()) {
            $sql = "INSERT INTO checks (task_id,group_id) 
        VALUES ('$this->task_id', '$this->group_id')";
            $myCon = ConfigClass::getConnection();
            if ($myCon->query($sql) === true) {
                return true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    public function getWeekDates($date) //this function returns the first and last day of the week
    {
        $week =  date('W', strtotime($date));
        $year =  date('Y', strtotime($date));
        $from = date("Y-m-d", strtotime("{$year}-W{$week}-1")); //Returns the date of monday in week
        $to = date("Y-m-d", strtotime("{$year}-W{$week}-7"));   //Returns the date of sunday in week

        $returnArray = array();
        $returnArray['start_date'] = $from;
        $returnArray['end_date'] = $to;
        return $returnArray;
    }



    public function isItIn() //this function check if the task is already checked
    {
        $today = Date("Y-m-d");

        $somedayLastWeek = Date("Y-m-d", strtotime("-7 days"));
        $weekDates = $this->getWeekDates($today);

        $start = $this->getWeekDates($today)['start_date'];
        $end = $this->getWeekDates($today)['end_date'];

        $isIt = false;
        $periodicity = Task::getPeriodicity($this->task_id);


        if ($periodicity == "daily") {
            //if the task is daily
            $sql = "SELECT * FROM `checks` WHERE created_at BETWEEN '$today 00:00:00' and '$today 23:59:59' and task_id='$this->task_id';";
        } elseif ($periodicity == "weekly") {
            //if the task is weekly check if it hasn't been checked last week
            $sql = "SELECT * FROM `checks` WHERE created_at BETWEEN '$start 00:00:00' and '$end 23:59:59' and task_id='$this->task_id';";
        }

        echo $sql; 

        $result = ConfigClass::getConnection()->query($sql);

        $results = array();

        if ($result->num_rows == 0) {
            $isIt = true;
        }

        return  $isIt ;
    }
    public function isUsersTask() 
    {
        if ($this->user_id == Task::getUserIdByTaskId($this->task_id)) {
            return true;
        }
        return false;
    }
}
