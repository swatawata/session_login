<?php
$loginEmail = 'root';
$loginPassword = '1111';

session_start();

$email = '';
$password = '';
$_SESSION['loginStatus'] = false;
$sessionEmail = ($_SESSION['email'] != '') ? $_SESSION['email'] : '';
$catchError = ($_SESSION['error'] != '') ? $_SESSION['error'] . "<br />": '';

$pushButton = isset($_POST['send']);

if ($pushButton) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == $loginEmail && $password == $loginPassword) {
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