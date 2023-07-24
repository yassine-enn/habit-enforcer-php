<?php
session_start();
require_once "config.php";
require_once "models/configClass.php";


require_once "models/group.php";
require_once "models/Task.php";


$message = '';


// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}


$admin_id        =  $_SESSION["id"];
$auth_id        =  $_SESSION["id"];

// Add new group
if (isset($_POST["name"])) {
    if (!User::hasGroup($auth_id)) {
        $group = new Group($_POST["name"], $auth_id);
        $group->save();
    } else {
        $message = "User already belongs to a group";
    }
}

?>

<?php require_once "includes/header.php"; ?>

<script>

var groupToBeDeleted = 0;

<?php

$user = User::getById($auth_id);

foreach($user->userGroups() as $group){ 
    if($group->groupScore()<0){
    echo 'groupToBeDeleted = '.$group->id.';';
  }
}

?>


</script>
<style>
  .bicon {
    font-size: 22px;
  }
</style>
<h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Habit enforcer.</h1>

<center>
  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modelId">
    Add a group
  </button>
</center>
<div class="col-md-4 offset-md-4">



  <hr>
  <?php
    if(!empty($message)){
      echo '<div class="alert alert-success">' . $message . '</div>';
    }  
  ?>
  <h3>My groups</h3>

  <ul class="list-group">

    <?php  $myGroups = Group::getJoinedGroupsForUser($auth_id);?>

    <?php foreach($myGroups as $group):?>
    <li class="list-group-item bicon">

      <div class="row">
        <div class="col-md-6">
          <?= strip_tags($group->text) ?> <span class="badge bg-<?= $group->groupColor() ?>"><?= $group->groupScore() ?></span> 

        </div>
        <div class="col-md-2">
          <a title="Invite users" href="group_show.php?id=<?= $group->id ?>"><i class=" bi bi-person-plus"></i></a>

        </div>
        <div class="col-md-2">
          <a title="Show tasks" href="group_tasks.php?id=<?= $group->id ?>"><i class=" bi bi-check2-square"></i></a>

        </div>
        <div class="col-md-2">
          <a title="Quit group"  href="./routes/quit_invite.php?membership=<?= $auth_id ?>"> <i class="bi bi-door-open"></i> </a>
        </div>
      </div>



    </li>
    <?php endforeach?>


  </ul>

  <hr />

</div>

<?php require_once "includes/footer.php"; ?>



<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add a group</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">

          <div class="form-group">
            <input type="text" name="name" id="name" class="form-control" placeholder="Group name"
              aria-describedby="helpId">
          </div>

          <button type="submit" class="btn btn-danger btn-block">Add</button>
          <br>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    if (groupToBeDeleted != 0) {
      alert("You have a negative score in a group. Please quit the group to continue");
      window.location.href = "./routes/quit_invite.php?membership=" + <?=$auth_id ?>;
    }
  });
</script>