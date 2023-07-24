<?php

include_once 'config.php';
include_once 'models/configClass.php';
include_once 'models/group.php';
include_once 'models/User.php';
include_once 'models/Check.php';
include_once 'models/Task.php';
include_once 'models/Log.php';



$groups = Group::getAll();


foreach($groups as $group){
    $tasks = $group->getTasks();

    foreach($tasks as $task){
        print_r($task->user_id);
        $log = new Log($task->id);

        $log->addNewTask();
        // $log->save();
        // print_r($log);
        //print last task entery use getLastTaskEntery function from log class
        // print_r($log->getLastTaskEntery($log->task_id));
    }
    echo '<br>******************<br>';

}

