<?php

session_start();
require_once "../config.php";

if (isset($_GET["membership"])) {
    $member_id      =  $_GET["membership"];

    echo $member_id;
    $sql = "delete from  group_members  where member_id = $member_id";
    $link->query($sql);

    header("location: ../index.php");
}
