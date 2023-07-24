<?php



?>


<?php
session_start();
require_once "config.php";
require_once "Models/configClass.php";
require_once "Models/group.php";
require_once "Models/User.php";
require_once "Models/Task.php";
$message = '';


// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


$admin_id        =  $_SESSION["id"];
$auth_id        =  $_SESSION["id"];
$group_id        = $_GET["id"];



// Add new group
if(isset($_POST["name"])){
    $username =  $_POST["name"];

    $user = User::getByUserName($username);

    if($user->username != ""){
        if(!User::hasGroup($user->id)){
            $sqlinsert = "INSERT INTO group_members (member_id,group_id)
            VALUES ('$user->id','$group_id')";
    
            if ($link->query($sqlinsert) === TRUE) {
                $message = "Invitation sent successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        else {
            $message = "User already belongs to a group";
        }
        
    }
    else{
        $message = "User not found";
    }

}

$currentGroup = Group::getById($group_id);
$members = Group::getGroupMembers($group_id);

?>

<?php require_once "includes/header.php"; ?>
<center>
    <br>
    <h2> Group details</h2>
    <!-- display group score -->
    <h3>Group score: <?php echo $currentGroup->groupScore(); ?></h3>
    <a href="group_tasks.php?id=<?php echo $group_id; ?>">Group tasks</a>
    <br>
</center>
<div class="col-md-4 offset-md-4">
    <form action="" method="post">

        <div class="form-group">
            <input type="text" name="name" id="name" class="form-control" placeholder="Username to add"
                aria-describedby="helpId">
        </div>

        <button type="submit" class="btn btn-danger btn-block">Add to group</button>
        <br>
        <?php
            if(!empty($message)){
                echo '<div class="alert alert-success">' . $message . '</div>';
            }  
            ?>
    </form>

    <hr>

    <ul class="list-group">
        <?php foreach($members as $member): ?>

            <li class="list-group-item text-<?= $member->status==0?'danger':'info'; ?>">
                <?= $member->username?>
                <?php if($member->status == 0 && $member->id == $auth_id): ?>
                    <a href="./routes/accept_invite.php?membership=<?= $member->membership ?>&gid=<?= $group_id ?>" class="btn btn-warning"> Accept </a>
                <?php endif ?>

                <?php if($member->id == $auth_id): ?>
                    <a href="./routes/quit_invite.php?membership=<?= $auth_id ?>" class="btn btn-danger"> Quit </a>
                <?php endif ?>
            
            </li>
        <?php endforeach ?>
    </ul>



</div>

<!-- FOOTER -->
<?php require_once "includes/header.php"; ?>