<?php

require('User.php');

class Group
{
    public $id;
    public $text;
    public $admin_id;


    public function __construct($text, $admin_id, $id="")
    {
        $this->id = $id;
        $this->text = $text;
        $this->admin_id = $admin_id;
    }


    public static function getAll()
    {
        $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if ($link === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }


        $sql = "SELECT * FROM groups";
        $result = ConfigClass::getConnection()->query($sql);

        $results = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $results[] = new Group($row['text'], $row['admin_id'], $row['id']);
            }
        }

        return $results;
    }

    public static function getForUser($user_id)
    {
        $sql = "SELECT *  FROM groups where admin_id = '$user_id' ";
        $result = ConfigClass::getConnection()->query($sql);

        $results = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $results[] = new Group($row['text'], $row['admin_id'], $row['id']);
            }
        }

        return $results;
    }

    public static function getJoinedGroupsForUser($user_id)
    {
        $sql = "SELECT g.text,g.id,gm.status,admin_id FROM `group_members` gm 
        JOIN groups g on g.id = gm.group_id 
        JOIN users u on u.id = gm.member_id where member_id = $user_id;";


        $result = ConfigClass::getConnection()->query($sql);

        $results = array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $results[] = new Group($row['text'], $row['admin_id'], $row['id']);
            }
        }

        return $results;
    }

    public static function getGroupMembers($group_id)
    {
        $sql = "SELECT u.id,gm.id as mid,group_id, username,status FROM users u
        join group_members gm on u.id = gm.member_id 
        
        where gm.group_id = $group_id
        ";
        $result = ConfigClass::getConnection()->query($sql);

        $users  =  array();

        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $user = new User();
                $user->username = $row['username'];
                $user->status = $row['status'];
                $user->id = $row['id'];
                $user->membership = $row['mid'];
                $users[] = $user;
            }
        }

        return $users;
    }

    public function save()
    {
        $this->text = filterXSSAndSQL($this->text);

        $sql = "INSERT INTO groups (text,admin_id)        
        VALUES ('".$this->text."','$this->admin_id')";

        $auth_id        =  $_SESSION["id"];

        $myCon = ConfigClass::getConnection();


        if ($myCon->query($sql) === true) {
            $this_id = $myCon->insert_id;

            $sql = "INSERT INTO group_members (group_id,member_id,status)
            VALUES ('$this_id','$auth_id','1')";
            if ($myCon->query($sql) === true) {
                // echo ' Greate';
            } else {
                echo "Error: " . $sql . "<br>" ;
            }
        } else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // public static function getGroupScore($group_id)
    // {
    //     $sql = "SELECT score FROM groups where id = $group_id";
    //     $result = ConfigClass::getConnection()->query($sql);

    //     $score = 0;

    //     if ($result->num_rows > 0) {
    //         // output data of each row
    //         while ($row = $result->fetch_assoc()) {
    //             $score = $row['score'];
    //         }
    //     }

    //     return $score;
    // }

    public function getTasks()
    {
        $sql = "SELECT * FROM tasks WHERE group_id = $this->id";

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
            // echo "0 results";
        }

        return $tasks;
    }

    public function groupScore()
    {
        $groupTasks = $this->getTasks();

        $groupScore =  (empty($groupTasks)) ? 1 : 0;


        foreach ($groupTasks as $task) {
            $score = $task->taskScore();
            $groupScore += $score;
        }
        return $groupScore;
    }

    public static function getById($id)
    {
        $sql = "SELECT * FROM groups where id = '$id'";

        $result = ConfigClass::getConnection()->query($sql);

        $group = new Group('', '', '');

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $group->id = $row['id'];
            $group->text = $row['text'];
            // $group->score = $row['score'];
        }

        return $group;
    }


    function groupColor(){ //changes the color of the score display based on how low or high the score is
        if($this->groupScore() == 0 ){
            return "warning";
        }
        else if($this->groupScore() < 0  ){
            return "danger";
        }
        else{
            return "success";
        }
    }

    function deleteGroup(){
        $sql = "DELETE FROM groups WHERE id = $this->id";

        $myCon = ConfigClass::getConnection();

        if ($myCon->query($sql) === true) {
            // echo "Record deleted successfully";
        } else {
            // echo "Error deleting record: " . $myCon->error;
        }
    }

}
