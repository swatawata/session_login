<?php
session_start();

$sessionEmail = (!empty($_SESSION['email'])) ? $_SESSION['email']: "";


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
    <div>
        <form action="./login.php" method="POST">
            <label for="email-label">メールアドレス:</label><br />
            <input type=“text” name="email" type="email" required value=<?php echo $sessionEmail; ?>><br />
            <label for="password-label">パスワード:</label><br />
            <input type="password" name="password"><br />
            <input type="submit" name="send" value="ログイン">
        </form>
    </div>
    <a href="./newAccount.php">アカウントを作る</a>
</body>

</html>