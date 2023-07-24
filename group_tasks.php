<?php
session_start();
require_once "config.php";
require_once "Models/configClass.php";
require_once "Models/group.php";
require_once "Models/User.php";
require_once "Models/Check.php";
require_once "Models/Task.php";
require_once "Models/Log.php";

$message = '';


// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$auth_id        =  $_SESSION["id"];
$group_id        = $_GET["id"];

$currentGroup = Group::getById($group_id);

//color the cards according to the color in the database
$tasks = Task::getTasksByGroup($group_id);
?>

<?php require_once "includes/header.php"; ?>
<center>
    <br>
    <h2> Group details</h2>
    <!-- display  -->
    <h3>Group score: <?php echo $currentGroup->groupScore(); ?></h3>
    <br>
</center>

<div class="container">

    <div class="row">
        <div class="col-md-6">

            <h4>Add Task</h4>
            <form action="./routes/tasks_add.php" method="post">

                <div class="form-group">
                    <input type="text" name="text" required class="form-control" placeholder="I wanna do ...">
                    <input type="text" name="gid" value="<?= $group_id ?>" hidden class="form-control" placeholder="">
                </div>

                <div class="form-group">
                    <input type="number" min="0" max="100" required name="difficulty" class="form-control"
                        placeholder="difficulty">
                </div>
                <div class="form-group">
                    <select type="text" name="periodicity" class="form-control" placeholder="periodicity">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="form-group">
                    <select type="text" name="color" class="form-control" placeholder="color">
                        <option value="red">Red</option>
                        <option value="#a4a408e0">Yellow</option>
                        <option value="green">Green</option>
                        <option value="blue">Blue</option>
                        <option value="grey">Grey</option>
                        <option value="#00008B">Dark Blue</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger btn-block">Add Task</button>
                <br>
                <?php
                    if(!empty($message)){
                        echo '<div class="alert alert-success">' . $message . '</div>';
                    }  
                ?>
            </form>

            <hr>

            <ul class="list-group">

            </ul>



        </div>
        <div class="col-md-6">

            <!-- display tasks in colors chosen by the user and add a button to check the task as done  -->
            <h4>Tasks</h4>

            <?php foreach($tasks as $task):?>

                <?php if(Log::taskLogCount($task->id) > 1 ):?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>The task <?=$task->text ?> of <?=$task->user()->username ?> has Lost <?=abs($task->taskScore() - Log::getLastTaskEntery($task->id)[1]['score']) ?> points !</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif?>
            <?php endforeach?>

            <div class="row">
                <?php foreach($tasks as $task):?>
                <div class="col-md-6">
                    <div class="card text-white mb-3" style="background-color:<?=$task->color ?>;"
                        style="max-width: 18rem;">
                        <div class="card-header">Task</div>
                        <div class="card-body">
                            <h5 class="card-title"><?=$task->text ?></h5>
                            <p class="card-text">Difficulty: <?=$task->difficulty ?> </p>
                            <p class="card-text">Checks count : <?=$task->checksCount(); ?> </p>
                            <p class="card-text">Task score : <?=$task->taskScore(); ?> </p>
                            <a href="./routes/tasks_check.php?tid=<?=$task->id."&gid=".$group_id ?>"
                                class="btn btn-primary">Done</a>
                        </div>
                    </div>
                </div>
                <?php endforeach?>
            </div>
        </div>
    </div>

</div>



<!-- FOOTER -->
<?php require_once "includes/footer.php"; ?>