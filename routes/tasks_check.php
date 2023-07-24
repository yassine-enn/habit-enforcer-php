<?php

require_once "../config.php";
require_once "../Models/configClass.php";
require_once "../Models/Check.php";
session_start();


if (isset($_GET["tid"])) {
    $gid = $_GET["gid"];
    $tid = $_GET["tid"];


    $check = new Check();
    $check->group_id = $gid;
    $check->task_id = $tid;
    $check->save();
}

header("location: ../group_tasks.php?id=$gid");
