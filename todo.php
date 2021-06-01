<?php

session_start();

$db['user_name'] = "root";
$db['password'] = "root";

$dbh = new PDO("mysql:host=localhost; dbname=todoList; charset=utf8", $db['user_name'], $db['password']);


$showAllTask = "";
$jobs = ["<div class='flex block mb-3'>", "<div class='mr-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'><a href=./todo.php?not-done><span class=text-white>未完了</span></a></div>", "<div class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'><a href=./todo.php?done><span class=text-white>完了</span></a></div><br />", "</div>"];

$list = (isset($_GET['done'])) ? "<h2 class='mb-2 text-3xl'>完了タスク一覧</h2>" : "<h2 class='mb-2 text-3xl'>未完了タスク一覧</h2>";

if ($_SESSION['loginStatus'] == true) {
    if (isset($_POST['search']) && $_POST['search'] != "") $_SESSION['search'] = $_POST['search'];

    //search
    $searchSql = "";
    $sortSql = "";
    $search = "";
    $sorts = [];

    if (isset($_GET['search'])) {
        $search = $_SESSION['search'];
        $searchSql = "&& contents LIKE '%$_SESSION[search]%'";
        $showAllTask = "<a class='text-blue-600' href=./todo.php>全件表示に戻す</a><br />";

        $dateAsc = (isset($_GET['done']))
            ? "<a class='text-blue-600' href=./todo.php?search=$_SESSION[search]&done&sort=asc>締め切り昇順</a>"
            : "<a class='text-blue-600' href=./todo.php?search=$_SESSION[search]&sort=asc>締め切り昇順</a>";
        $dateDesc = (isset($_GET['done']))
            ? "<a class='text-blue-600' href=./todo.php?search=$_SESSION[search]&done&sort=desc>締め切り降順</a><br />"
            : "<a class='text-blue-600' href=./todo.php?search=$_SESSION[search]&sort=desc>締め切り降順</a><br />";
    } else {
        $dateAsc = (isset($_GET['done']))
            ? "<a class='text-blue-600 mr-3' href=./todo.php?done&sort=asc>締め切り昇順</a>"
            : "<a class='text-blue-600 mr-3' href=./todo.php?sort=asc>締め切り昇順</a>";
        $dateDesc = (isset($_GET['done']))
            ? "<a class='text-blue-600' href=./todo.php?done&sort=desc>締め切り降順</a><br />"
            : "<a class='text-blue-600' href=./todo.php?sort=desc>締め切り降順</a><br />";
    }

    //sort
    if (isset($_GET['sort']) && isset($_GET['search'])) {
        if ($_GET['sort'] == "asc") $sortSql = "order by deadline ASC";
        elseif ($_GET['sort'] == "desc") $sortSql = "order by deadline DESC";
    } elseif (isset($_GET['sort'])) {
        if ($_GET['sort'] == "asc") $sortSql = "order by deadline ASC";
        elseif ($_GET['sort'] == "desc") $sortSql = "order by deadline DESC";
    }
    $sorts = ["<div class='mb-6'>", $dateAsc, $dateDesc, "</div>"];

    //done or not done
    $statusSql = "";
    $statusSql = "status = 0 &&";
    if (isset($_GET['not-done'])) $statusSql = "status = 0 &&";
    elseif (isset($_GET['done'])) $statusSql = "status = 1 &&";


    //default task list
    $sql = "select * from tasks where $statusSql user_id=$_SESSION[userId] $searchSql $sortSql";
    $res = $dbh->query($sql);
    $noTasks = "";
    $complete = "";
    $taskList = [];
    $job = (isset($_GET['done'])) ? "未完了に戻す" : "完了";

    foreach ($res as $key => $task) {
        $check = "";
        $tasks[] = $task['contents'];

        //form
        $complete = (isset($_GET['search']))
            ? "<a class='text-blue-600' href=./todo.php?search=$search&status=$task[status]&contents=$task[contents]>$job</a><br />"
            : "<a class='text-blue-600' href=./todo.php?status=$task[status]&contents=$task[contents]>$job</a><br />";


        $taskList[] = "<p class='mb-3'>$task[contents] $task[deadline] $complete</p>";
    }
    if (count($taskList) == 0) $noTasks = "<p>現在タスクはありません</p>";
}


//append tasks
if (isset($_POST['append'])) {
    if (!empty($_POST['task']) && !empty($_POST['deadline'])) {
        $_SESSION['task'] = $_POST['task'];
        $timeStanp = date("Y-m-d");
        $sql = "INSERT INTO tasks(id, user_id, status, contents, deadline, created_at, update_at) VALUES (0, $_SESSION[userId], 0, '$_SESSION[task]', '$_POST[deadline]', '$timeStanp', '$timeStanp')";
        $res = $dbh->query($sql);
    }
    header("Location: $_SERVER[REQUEST_URI]");
    exit;
}

//logout
$setLogout = isset($_GET['logout']);
if ($setLogout) $_SESSION['loginStatus'] = false;


//complete click
$contents = "";
if (isset($_GET['status'])) {
    $statusId = $_GET['status'];
    $statusId = ($statusId == 0) ? 1 : 0;

    $contents = $_GET['contents'];
    $sql = "update tasks set status = $statusId where user_id = $_SESSION[userId] && contents = '$contents'";
    $res = $dbh->query($sql);
    header("Location: ./todo.php");
    exit;
}


$logout = "<a href=./?logout><span class='text-white leading-9'>ログアウト</span></a><br />";

$searchAction = (isset($_GET['done'])) ? "./todo.php?search=$_SESSION[search]&done" : "./todo.php?search=$_SESSION[search]";

if (isset($_POST['search']) && empty($_POST['search'])) {
    header("Location: ./todo.php");
    exit;
}

$searchForm = "
    <div class='mb-2'>
        <form class='mb-12' action=$searchAction method=POST>
        <input class='border-2 border-gray-300' type=search name=search placeholder=キーワードを入力>
        <input class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded' type=submit name=submit value=検索>
        </form>
    </div>
";

$appendTask = "
        <div class='mb-2'>
            <form action=./todo.php?append method=POST>
            <input class='border-2 border-gray-300' type=text name=task placeholder=タスクを追加>
            <input class='border-2 border-gray-300' type=text name=deadline placeholder=締め切り日を入力>
            <input class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded' type=submit name=append value=追加>
            </form>
        </div>
";

$taskForm = ["<div class='mb-2'>", "<form class='pb-6' action=$_SERVER[REQUEST_URI] method=POST>", implode("\n", $taskList), "</form>", "</div>"];

$header = "<div class='mb-3'><span>タスク名</span> <span>|</span> <span>締め切り</span></div>";


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>todo view</title>
    <link rel="stylesheet" href="./src/tailwind.css">
</head>

<body class="bg-gray-300">
    <div class="mx-auto md:w-7/12 w-11/12 bg-white mb-6">
        <header class="text-right mb-14 bg-blue-500"><?php echo $logout ?></header>
        <div class="w-9/12 mx-auto">
            <?php
            //login
            if ($_SESSION['loginStatus'] == true) {

                echo implode("", $jobs);
                echo $searchForm;
                echo $list;
                echo implode("", $sorts);
                echo "$showAllTask";
            }

            echo $noTasks;
            if (isset($_GET['done'])) echo "";
            else echo "<a class='text-blue-600 text-5xl block mb-6' href=./todo.php?new-task=true>+</a>";
            echo $header;
            if (filter_input(INPUT_GET, 'new-task', FILTER_VALIDATE_BOOL)) echo $appendTask;
            echo implode("\n", $taskForm);
            ?>
        </div>
    </div>
</body>

</html>