<?php

require_once "../config.php";
require_once "../Models/configClass.php";
require_once "../Models/Task.php";
session_start();
$auth_id        =  $_SESSION["id"];

if (isset($_POST["text"])) {
    $gid = $_POST["gid"];


    $task = new Task($auth_id );

    $task->text = $_POST["text"];
    $task->group_id = $_POST["gid"];
    $task->difficulty = $_POST["difficulty"];
    $task->color = $_POST["color"];
    $task->repeats = 0;
    $task->periodicity = $_POST["periodicity"];


    $task->save();
}

header("location: ../group_tasks.php?id=$gid");
