<?php 
    session_start();
    $_SESSION['loginStatus'] = false;
    header("Location: ./index.php");
    exit;
?>
