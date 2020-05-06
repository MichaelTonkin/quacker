<!--
File: index.php
Description: Contains the messaging page and login page logic and ui.
Warning: Git reporsitory does not include connection variables for security purposes.
Author: Michael Tonkin.
-->

<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

<?php

include "styles.css";
include "connect.php";
include "helper.php";

echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

//connect to db
$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

/*
Funciton: login
Description: presents the user with a login screen. Upon clicking "submit", the user's ip and chosen username will be stored in the database for later use.
*/
function login()
{
	echo '
	<div id="loginContainer">
	<p>Please enter a username. This will be displayed on all of your messages and cannot be changed.</p>
	<form action="newuser.php" method="post">
	<input type="text" name="username" />
	<input type="submit" value="Submit" />
	</form>
	</div>
	';
}

/*
Function: hideLogin
Description: hides the login screen. Should only be used once after user has logged in.
*/
function hideLogin()
{
	echo'
	<style>
	#loginContainer
	{
		display:hidden;
	}
	</style>
	';
}

/*
Function: mainPage
Description: Provides ui and logic for messaging service.
*/
function mainPage()
{

	$ip = getUserIpAddr();
	$username = getSingle("select username from twitUsers where ip = '".$ip."'");

	//handle input when user hits 'quack' button
	if(isset($_REQUEST['quack'])) 
	{
		$quack = $_REQUEST['quack'];
		$uid = getSingle("select uid from twitUsers where ip = '".$ip."'");

		$date = Date("Y-m-d H:i:s");
		query("insert into twitTweets(uid, post, date, username) values ('$uid', '$quack', '$date', '$username')");
	}

	//this is the quack input field (for users making quacks).
	echo '
	<form class="form-group" method="POST" action="index.php">
	<textarea type="text" name="quack" class="form-control-plaintext form-control-lg" id="msg-box" placeholder="What\'s on your mind?" maxlength="140"></textarea>
	<input type=submit value="Quack" class="btn" id="btn-tweet">
	</form>
	';

	//here we iterate through each message in the database and create a table row for presenting to the user.
	$result = query("select * from twitTweets order by date desc");
	echo "<table class='table table-hover'>";
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$uid = $row['uid'];
		$post = htmlspecialchars($row['post']);
		$date = $row['date'];
		echo "
		<tr class='msg-id-display'>
		<td> <img src='img/default-profile.png' class='profile-img'></td> <td class='uid-container'><span>$username</span> </td>
		<td> <span class='msg-body'>$post</span> </td>
		<td> <span class='date-container'>$date</span> </td>
		</tr>
		";
	}
	echo "</table>";
}

//------------------
//driver code
//-----------------

$ip = getUserIpAddr();
$uid = getSingle("select uid from twitUsers where ip = '".$ip."'"); //check if user already exists in our db

//if the user has not connected before then allow them to create an account
if(!$uid)
{
	login();
}
else //otherwise go to the mainpage
{
	mainPage();
}

?>
