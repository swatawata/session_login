<?php
session_start();

$db['user_name'] = "root";
$db['password'] = "root";

$dbh = new PDO("mysql:host=localhost; dbname=todoList; charset=utf8", $db['user_name'], $db['password']);

$sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL
        )";

$res = $dbh->query($sql);

$email = '';
$password = '';
$_SESSION['loginStatus'] = false;
$count = 0;

if (!isset($_SESSION['email'])) $_SESSION['email'] = '';
if (!isset($_SESSION['error'])) $_SESSION['error'] = '';

$sessionEmail = ($_SESSION['email'] != '') ? $_SESSION['email'] : '';
$catchError = ($_SESSION['error'] != '') ? $_SESSION['error'] . "<br />" : '';

$clickButton = $_SERVER["REQUEST_METHOD"] == "POST";

if ($clickButton) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "select * from users where email LIKE '$email%'";
    $res = $dbh->query($sql);
    $count = $res->rowCount();
    if ($count > 0) {
        $sql = "select * from users where email LIKE '$email%'";
        $res = $dbh->query($sql);

        foreach ($res as $value) {
            if ($value[1] == $email && password_verify($password, $value[2])) {
                $_SESSION['email'] = '';
                $_SESSION['error'] = '';
                $_SESSION['loginStatus'] = true;
            } else {
                $_SESSION['email'] = $email;
                $_SESSION['error'] = 'ログインできませんでした';
                header("Location: ./");
                exit;
            }
        }
    }
}



$form = '
    <div>
        <form  action="./" method="POST">
            <label for="email-label">メールアドレス:</label><br />
            <input type=“text” name="email" type="email" required value=' . $sessionEmail . '><br />
            <label for="password-label">パスワード:</label><br />
            <input type="password" name="password"><br />
            <input type="submit" name="send" value="ログイン">
        </form>
    </div>
';

$logout = '<a href="./?logout">logout</a>';

$setLogout = isset($_GET['logout']);
if ($setLogout) $_SESSION['loginStatus'] = false;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>

<body>
    <h2>ログイン</h2>
    <?php
    //login
    if ($_SESSION['loginStatus'] == true) {
        echo $logout;
    }
    //not login
    if ($_SESSION['loginStatus'] == false) {
        echo $form;
        echo $catchError;
        echo '<a href="./newAccount.php">アカウントを作る</a>';
    }
    ?>
</body>

</html>