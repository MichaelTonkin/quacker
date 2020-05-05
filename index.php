<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>

<?php

include "styles.css";
//include "newuser.php";

echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

$dbhost = "185.224.138.28";
$dbuser = "u181092848_admin";
$dbpass = "AppendixB";
$dbname = "u181092848_forum";

//connect to db
$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

//helper function to quickly make a query
function query($query) 
{
	global $connect;
	$result = mysqli_query($connect, $query);
	return $result;
	
}

//issues a query and returns a single value
function getSingle($query)
{
	$result = query($query);
	$row = mysqli_fetch_row($result);
	return $row[0];
}

//generates a popup for account creation
function popupLogin()
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

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


function mainPage()
{
	//handle input when user hits 'quack' button
	if(isset($_REQUEST['quack'])) 
	{
		$quack = $_REQUEST['quack'];
		$ip = getUserIpAddr();
		$uid = getSingle("select uid from twitUsers where ip = '".$ip."'");
		$username = getSingle("select username from twitUsers where ip = '".$ip."'");
		
		//create user id if it does not exist
		/*if(!$uid)
		{
			query("insert into twitUsers(ip) values ('$ip')");
		}
		*/
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

//handle first time login
$ip = getUserIpAddr();
$uid = getSingle("select uid from twitUsers where ip = '".$ip."'"); //check if user already exists in our db
if(!$uid)
{
	popupLogin();
}
else
{
	mainPage();
}

?>
