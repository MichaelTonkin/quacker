<?php
include 'index.php';

$ip = mysqli_real_escape_string($connect, $_SERVER['REMOTE_ADDR']);
$username = mysqli_real_escape_string($connect, $_POST["username"]);

query("insert into twitUsers(ip, username) values ('$ip', '$username')");

hideLogin();
mainPage();

?>