<?php
session_start();
require_once "config.php";
require_once "Models/configClass.php";
require_once "Models/group.php";
require_once "Models/User.php";
require_once "Models/Check.php";
require_once "Models/Task.php";
require_once "Models/Log.php";

$task = Task::getById(10);

print_r($task);
echo '<br>******************<br>';
echo $task->taskScore();

if(Log::taskLogCount($task->id) > 1 ){
    echo '<br>******************<br>';
    echo Log::getLastTaskEntery($task->id)['score'];
}

