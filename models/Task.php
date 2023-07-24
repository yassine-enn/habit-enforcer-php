<?php

class Task
{
    public const DAILY = 0;
    public const WEEKLY = 1;


    public $id;
    public $text;
    public $repeats=0;
    public $color;
    public $difficulty;
    public $periodicity;
    public $created_at;

    public $group_id;
    public $user_id;

    public function __construct($auth_id = null)
    {
        $this->user_id = $auth_id;
    }



    public function save()
    {
        if ($this->TaskLimit()) {
            $sql = "INSERT INTO tasks (text,repeats,color,difficulty,periodicity,group_id,user_id) 
        VALUES ('$this->text', '$this->repeats', '$this->color', '$this->difficulty', '$this->periodicity', '$this->group_id', '$this->user_id')";

            $myCon = ConfigClass::getConnection();

            if ($myCon->query($sql) === true) {
                return true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    public static function getTasksByGroup($group_id)
    {
        $sql = "SELECT * FROM tasks WHERE group_id = $group_id";

        $myCon = ConfigClass::getConnection();

        $result = $myCon->query($sql);

        $tasks = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $task = new Task();
                $task->id = $row["id"];
                $task->text = $row["text"];
                $task->repeats = $row["repeats"];
                $task->color = $row["color"];
                $task->difficulty = $row["difficulty"];
                $task->periodicity = $row["periodicity"];
                $task->created_at = $row["created_at"];
                $task->group_id = $row["group_id"];
                $task->user_id = $row["user_id"];


                array_push($tasks, $task);
            }
        } else {
            echo "0 results";
        }

        return $tasks;
    }


    public static function getById($id)
    {
        $sql = "SELECT * FROM tasks where id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);

        $task = new Task();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $task->id = $row['id'];
            $task->text = $row['text'];
            $task->repeats = $row['repeats'];
            $task->color = $row['color'];
            $task->difficulty = $row['difficulty'];
            $task->periodicity = $row['periodicity'];
            $task->repeats = $row['repeats'];
            $task->group_id = $row['group_id'];
            $task->user_id = $row['user_id'];
            $task->created_at = $row['created_at'];
        }

        return $task;
    }

    public static function fromArray($row)
    {

        $task = new Task();

        $task->id = $row['id'];
        $task->text = $row['text'];
        $task->repeats = $row['repeats'];
        $task->color = $row['color'];
        $task->difficulty = $row['difficulty'];
        $task->periodicity = $row['periodicity'];
        $task->repeats = $row['repeats'];
        $task->group_id = $row['group_id'];
        $task->user_id = $row['user_id'];
        $task->created_at = $row['created_at'];
        

        return $task;
    }

    public function checksCount()
    {
        $sql = "SELECT count(*) as checksCount FROM checks where task_id = '$this->id'";

        $result = ConfigClass::getConnection()->query($sql);

        $num = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $num = $row['checksCount'];
        }

        return $num;
    }

    public function createdSince()
    {
        $origin = new DateTimeImmutable($this->created_at);
        $target = new DateTime();
        $interval = $origin->diff($target);
        if ($this->periodicity == 'weekly') {
            return floor($interval->format('%a') / 7);
        }
        return $interval->format('%a');
    }

    public function getChecks()
    {
        $sql = "SELECT * FROM checks WHERE task_id = $this->id";

        $myCon = ConfigClass::getConnection();

        $result = $myCon->query($sql);

        $checks = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $check = new Check();

                $check->id = $row["id"];
                $check->created_at = $row["created_at"];

                $checks[] = $check;
            }
        } else {
            echo "0 results";
        }

        return $checks;
    }

    public function TaskLimit()
    {
        $today = Date("Y-m-d");
        $alreadyCreated = false;


        $sql = "SELECT * FROM `tasks` WHERE created_at BETWEEN '$today 00:00:00' and '$today 23:59:59' and user_id='$this->user_id';";

        echo $sql;
        $result = ConfigClass::getConnection()->query($sql);

        $results = array();

        if ($result->num_rows == 0) {
            $alreadyCreated = true;
        }

        return  $alreadyCreated ;
    }

    public static function getPeriodicity($id)
    {
        $sql = "SELECT periodicity FROM tasks where id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);

        $periodicity = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $periodicity = $row['periodicity'];
        }

        return $periodicity;
    }
    public static function getUserIdByTaskId($id)
    {
        $sql = "SELECT user_id FROM tasks where id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);

        $user_id = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $user_id = $row['user_id'];
        }

        return $user_id;
    }

    public function user()
    {
        $sql = "SELECT * FROM users where id = '$this->user_id'";

        $result = ConfigClass::getConnection()->query($sql);

        $user = new User();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $user->username = $row['username'];
        }

        return $user;
    }

    public function taskScore()
    {
        $baseCount =  $this->createdSince();
        $checksCount = $this->checksCount();

        $pointsToRemove =  $baseCount - $checksCount;

        $pointsToRemove = $pointsToRemove < 0 ? 0 : $pointsToRemove;

        $taskScore = $checksCount - $pointsToRemove;
        //          3   data            5           3


        // $taskScore = $checksCount - ($baseCount- $checksCount);



        return $taskScore;
    }

    public function UITaskScore()
    {
        $taskScore = $this->taskScore();



        return $taskScore * $this->difficulty;
    }
}
