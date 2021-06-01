<?php

session_start();

if ($_SESSION['loginStatus'] == false) {
    header("Location: ./login_form.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="./logout.php">ログアウト</a>
    
</body>
</html>