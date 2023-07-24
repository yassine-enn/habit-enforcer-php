<?php

session_start();
require_once "../config.php";

if (isset($_GET["membership"])) {
    $membership_id      =  $_GET["membership"];
    $gid                =  $_GET["gid"];

    echo $membership_id;
    $sql = "update group_members set status = 1 where id = $membership_id";
    $link->query($sql);

    header("location: ../group_show.php?id=$gid");
}
