<?php
session_start();
require_once "config.php";
require_once "models/configClass.php";


require_once "models/group.php";


$message = '';


// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
header("location: login.php");
exit;
}


$admin_id        =  $_SESSION["id"];
$auth_id        =  $_SESSION["id"];

// Add new group
if(isset($_POST["name"])){

$group = new Group($_POST["name"], $auth_id);
$group->save();
}

?>

<?php require_once "includes/header.php"; ?>
<center>
  <br>
  <h2> Groups</h2>
  <br>
</center>
<div class="col-md-4 offset-md-4">
  <form action="" method="post">

    <div class="form-group">
      <input type="text" name="name" id="name" class="form-control" placeholder="Group name"
        aria-describedby="helpId">
    </div>

    <button type="submit" class="btn btn-danger btn-block">Add</button>
    <br>
    <?php
        if(!empty($message)){
          echo '<div class="alert alert-success">' . $message . '</div>';
        }  
      ?>
  </form>


  <hr>

  <h3>My groups</h3>
  <ul class="list-group">

    <?php 

        $myGroups = Group::getJoinedGroupsForUser($auth_id);

        foreach($myGroups as $group){
          echo '<li class="list-group-item"><a href="group_show.php?id='.$group->id.'">'.$group->text.'</a></li>';
        }

      ?>

  </ul>

  <hr />

</div>

<?php require_once "includes/footer.php"; ?>
