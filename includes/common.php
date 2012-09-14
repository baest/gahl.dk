<?php
session_start();

function get_message($type) {
	return "SUCKER";
	// $query = "SELECT * FROM messages WHERE message_type='cmd_not_found' order by rand() limit 1";
	// $result = mysql_query($query) or die(mysql_error());
	// if($row = mysql_fetch_assoc($result)){
	// 	eval($row['code']);
	// }
	// else {
	// 	echo "ARGH!!! det sker jo aldrig det her...";
	// }
}

function current_user() {
	
	$query = "SELECT * FROM people where id=".$_SESSION['people_id'];
	$result = mysql_query($query) or die(mysql_error().$query);
	$user = mysql_fetch_object($result);
	
	
	return $user;
}

function user_login() {

	if(!isset($_SESSION['people_id'])) {

		$query = "SELECT id FROM people where id<>1 order by last_used limit 1";
		$result = mysql_query($query) or die(mysql_error().$query);
		$row = mysql_fetch_assoc($result);
		$user_id = $row['id'];
	
		$query = "update people set last_used=now() where id=".$user_id;
		mysql_query($query) or die(mysql_error().$query);

		$_SESSION['people_id'] = $user_id;
	}


}

?>