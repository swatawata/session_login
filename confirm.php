<?php
session_start();

$passwordCount = array_fill(0, strlen($_SESSION['password']), '*');
$showPassword = implode('', $passwordCount);

$confirm = "<div>
                <p>こちらの内容で登録します</p>
                <p>メールアドレス：</p>
                <p>" . $_SESSION['email'] . "</p>
                <p>パスワード：</p>
                <p>" . $showPassword . "</p>
                <form  method=POST>
                <input type=submit name=return value=戻る>
                <input type=submit name=regist value=登録>
                </form>
            </div> ";

if (isset($_POST["return"])) {
    header("Location: ./newAccount.php");
    exit;
} elseif (isset($_POST["regist"])) {

    $user_name = "root";
    $password = "root";

    $dbh = new PDO("mysql:host=localhost; dbname=todoList; charset=utf8", $user_name, $password);

    // $sql = "
    //     CREATE TABLE users (
    //         id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    //         email VARCHAR(255) NOT NULL,
    //         password VARCHAR(64) NOT NULL
    //     )";

    $sql = "INSERT INTO users(email, password) VALUES ('$_SESSION[email]', '$_SESSION[password]')";
    var_dump($sql);
    $res = $dbh->query($sql);

    $_SESSION = [];
    header("Location: ./end.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php echo $confirm; ?>
</body>

</html>