<?php

session_start();

$db['user_name'] = "root";
$db['password'] = "root";

$pdo = new PDO("mysql:host=localhost; dbname=todoList; charset=utf8", $db['user_name'], $db['password']);

$sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )";

$res = $pdo->query($sql);

$sql = "
        CREATE TABLE IF NOT EXISTS tasks(
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            status INT NOT NULL DEFAULT 0,
            contents VARCHAR(255) NOT NULL DEFAULT '',
            deadline date NOT NULL ,
            created_at date NOT NULL, 
            update_at date NOT NULL
        )";

$res = $pdo->query($sql);

$email = '';
$password = '';
$_SESSION['loginStatus'] = false;
$count = 0;

if (!isset($_SESSION['email'])) $_SESSION['email'] = '';
if (!isset($_SESSION['error'])) $_SESSION['error'] = '';

$sessionEmail = ($_SESSION['email'] != '') ? $_SESSION['email'] : '';
$catchError = "";

$clickButton = $_SERVER["REQUEST_METHOD"] == "POST";

if ($clickButton) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "select * from users where email = ?";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(1, $email, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch();

    if (!$result) {
        //エラー回避
    } elseif (password_verify($password, $result["password"])) {
        $_SESSION['loginStatus'] = true;
        header("Location: ./index.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <div>
        <p>ログインできませんでした</p> 
        <a href="./login_form.php"><span>戻る</span></a>
    </div> 
</body>
</html>