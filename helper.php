<?php
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
?>