<?php
session_start();

$db['user_name'] = "root";
$db['password'] = "root";

$dbh = new PDO("mysql:host=localhost; dbname=todoList; charset=utf8", $db['user_name'], $db['password']);

$showAllTask = "";
$logout = '<a href="./?logout">logout</a><br />';

$searchForm = "
    <div>
        <form action=./todo.php?search=$_SESSION[search] method=POST>
        <input type=search name=search placeholder=キーワードを入力>
        <input type=submit name=submit value=検索>
        </form>
    </div>
";
$appendTask = "
        <div>
            <form action=./todo.php?append method=POST>
            <input type=text name=task placeholder=タスクを追加>
            <input type=submit name=append value=追加>
            </form>
        </div>
";


if ($_SESSION['loginStatus'] == true) {
    if (isset($_POST['search']) && $_POST['search'] != "") $_SESSION['search'] = $_POST['search'];

    //search
    $searchSql = "";
    $sortSql = "";
    $search = "";
    $sorts = [];
    $actionLocation = "./todo.php";

    if (isset($_GET['search'])) {
        $search = $_SESSION['search'];
        $searchSql = "&& contents LIKE '%$_SESSION[search]%'";
        $showAllTask = "<a href=./todo.php>全件表示に戻す</a><br />";
        $actionLocation = "./todo.php?search=$_SESSION[search]";

        $dateAsc = "<a href=././todo.php?search=$_SESSION[search]&sort=1>日付昇順</a>";
        $dateDesc = "<a href=././todo.php?search=$_SESSION[search]&sort=2>日付降順</a><br />";
    } else {
        $dateAsc = "<a href=./todo.php?sort=1>日付昇順</a>";
        $dateDesc = "<a href=./todo.php?sort=2>日付降順</a><br />";
    }

    //sort
    if (isset($_GET['sort'])) {
        if ($_GET['sort'] == 1) {
            $sortSql = "order by deadline ASC";
            $actionLocation = "./todo.php?search=$_SESSION[search]&sort=1";
        } elseif ($_GET['sort'] == 2) {
            $sortSql = "order by deadline DESC";
            $actionLocation = "./todo.php?search=$_SESSION[search]&sort=2";
        }
    }

    $sorts = [$dateAsc, $dateDesc];

    //default task list
    $sql = "select * from tasks where user_id=$_SESSION[userId] $searchSql $sortSql";
    var_dump($sql);
    $res = $dbh->query($sql);
    $noTasks = "";
    $location = "";
    $taskList = [];
    foreach ($res as $key => $task) {
        $check = "";
        $tasks[] = $task['contents'];
        if ($task['status'] == 1) $check = "checked=checked";

        if (isset($_GET['search'])) $location = "<a href=./todo.php?search=$search&complete=$task[contents]>完了</a><br />";
        else $location = "<a href=./todo.php?complete=$task[contents]>完了</a><br />";

        $taskList[] = "<input type=hidden name=checkbox[$key] value=0><input type=checkbox name=checkbox[$key] value=1 $check>$task[contents] $task[deadline] $task[created_at] $task[update_at] $location";
    }
    if (count($taskList) == 0) $noTasks = "<p>現在タスクはありません</p>";

    //click checkbox
    $_SESSION['saved'] = "";
    $searchSql = " && contents LIKE '%$_SESSION[search]%'";
    if (isset($_POST['checkbox'])) {
        $checkboxies = $_POST['checkbox'];
        $sql = "";
        $sql = (isset($_GET['search'])) ? "select * from tasks where user_id=$_SESSION[userId]$searchSql" : "select * from tasks where user_id=$_SESSION[userId]";

        $res = $dbh->query($sql);

        $save = false;
        foreach ($res as $key => $task) {
            if ($checkboxies[$key] == $task['status']) continue;
            $statusId = $checkboxies[$key];
            $sql = "";
            $sql = "update tasks set status = $statusId where user_id = $_SESSION[userId] && contents = '$task[contents]'";
            var_dump($sql);
            var_dump();
            $dbh->query($sql);
            $save = true;
        }

        //save alert
        if ($save) {
            $_SESSION['saved'] = "<p>セーブされました</p>";
            // header("Location: $actionLocation");
            // exit;
        }
    }
}

//append tasks
if (isset($_POST['append'])) {
    $_SESSION['task'] = $_POST['task'];
    $timeStanp = date("Y-m-d");
    $sql = "INSERT INTO tasks(id, user_id, status, contents, deadline, created_at, update_at) VALUES (0, $_SESSION[userId], 0, '$_SESSION[task]', '$timeStanp', '$timeStanp', '$timeStanp')";
    $res = $dbh->query($sql);
}

//logout
$setLogout = isset($_GET['logout']);
if ($setLogout) $_SESSION['loginStatus'] = false;



//complete click
if (isset($_GET['complete'])) {
    $complete = $_GET['complete'];
    $sql = "DELETE FROM `tasks` WHERE user_id=$_SESSION[userId] && contents='$complete'";
    $res = $dbh->query($sql);
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>todo view</title>
</head>

<body>
    <?php

    //login
    if ($_SESSION['loginStatus'] == true) {
        echo $logout;
        echo $searchForm;
        echo "<h2>タスク一覧</h2>";
        echo implode(" | ", $sorts);
        echo "$showAllTask";
    }

    ?>
    <?php
    echo $noTasks;
    echo "<a href=./todo.php?new-task>+</a>";
    if (isset($_GET['new-task'])) echo $appendTask;
    ?>
    <div>
        <form action="<?php echo $actionLocation; ?>" method="POST">
            <?php echo implode("\n", $taskList); ?>
            <input type="submit" name="update" value="更新">
        </form>
    </div>
    <?php
    if (!empty($_SESSION['saved'])) echo $_SESSION['saved'];
    ?>
</body>

</html>