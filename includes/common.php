<?php
session_start();

function get_message($type) {
	$quote = get_random_quote()->{'quote'};
	return "SUCKER... men som man siger:<br /><em>&quot;$quote&quot;</em>";
	//return "SUCKER";
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

function get_random_quote() 
{
	$quote_json_url = 'http://iheartquotes.com/api/v1/random?max_lines=2&max_characters=100&show_source=false&show_permalink=false&format=json';
	$ch = curl_init( $quote_json_url );
	 
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
		CURLOPT_POSTFIELDS => $quote_json_url
	);
	 
	curl_setopt_array( $ch, $options );
	 
	$result = json_decode(curl_exec($ch));

	return $result;
}


?>