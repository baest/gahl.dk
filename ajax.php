<?php
require_once('includes/db_open.php');
require_once('includes/common.php');
include_once('includes/Services_JSON.php');


$function_to_call = allowed_functions_only($_REQUEST['fkt']);
call_user_func($function_to_call);

function allowed_functions_only($fkt) {
	$allowed_functions = array('command', 'update_console', 'update_console_and_wall');
	if(in_array($fkt, $allowed_functions)) {
		return $fkt;
	}
	else {
		return 'fuck_off';
	}
}

/**
* Returnerer JSON array med:
* [0] = wall opdateringer (tomt hvis der er ikke er nye opdateringer)
* 	[0] = det højeste wall_id der returneres
*	[1] = array af nye wall posts
* [1] = console input opdateringer (tomt hvis der ikke er nye opdateringer)
* 	[0] = det højeste command_id der returneres
* 	[1] = array af nye kommandoer
*/
function update_console_and_wall() {
	$json = new Services_JSON();
	$data = array(array(), array());

	//Hent wall opdateringer
	$query = "SELECT w.ID, w.Post, DATE_FORMAT(w.date, '%d.%m.%y %H:%i') as formated_date, p.name as people_id FROM wall w inner join people p on (w.people_id=p.id) WHERE w.ID>" . mysql_real_escape_string( $_REQUEST['last_wall_id'] ) . " order by w.ID";
	$result = mysql_query($query) or die(mysql_error().$query);
	while($row = mysql_fetch_assoc($result)) {
		$data[0][0] = $row['ID'];
		$data[0][1][] = array( $row['formated_date'], $row['Post'], $row['people_id'] );
		//$data[0][1] = array( $query, $row['name'] );
	}	

	// Hent input console opdateringer
	$query = "SELECT cl.id, cl.command, p.name FROM commandsLog cl inner join people p on (cl.people_id=p.id) WHERE people_id<>".current_user()->id." AND cl.id>" . mysql_real_escape_string( $_REQUEST['last_commands_log_id'] ) . " order by cl.id";
	$result = mysql_query($query) or die(mysql_error().$query);
	while($row = mysql_fetch_assoc($result)) {
		$data[1][0] = $row['id'];
		$data[1][1][] = array( $row['command'], $row['name'] );
	//	$data[1][1] = array( $query, $row['name'] );
	}

	echo $json->encode($data);
}

function write_to_wall($safe_content, $a_human_writes_this=true) {
	if($a_human_writes_this)
		$people_id = current_user()->id;
	else
		$people_id = 1;
		
	$safe_content = htmlspecialchars($safe_content);	
	
	$sql = sprintf("INSERT INTO wall (Post, date, people_id) VALUES('%s', now(), %d)", mysql_real_escape_string($safe_content), mysql_real_escape_string($people_id));
	
	$result = mysql_query($sql) or die(mysql_error().$sql);
	
	if($a_human_writes_this) {
		$system_comment = wallanswer($safe_content);
		if($system_comment && $system_comment != '') {
			write_to_wall($system_comment, false );
		}
	}
}

function command() {
	// Det er kommandoer der ikke bliver slået op i DBen, men som er hard code implementeret.
	$true_commands = array("wall", "væg", "skriv", "whatup");

	$command = strtolower(strtok($_REQUEST['command'], ' '));

	if ($command == 'define') {
		return define_command($_REQUEST['command']);
	}
	
	if(in_array($command, $true_commands)) {
		if($command=='wall' || $command=='væg' || $command=='skriv') {
			$message = mysql_real_escape_string( substr($_REQUEST['command'], 0+strpos($_REQUEST['command'], ' ') ) );
			if($message != '' && strlen($message)>0) {
				write_to_wall( $message );
				echo "Det ryger på væggen";
			}
			else {
				echo "Skriv noget mere for at få det på væggen - nød!";
			}
		}
		elseif ($command=='whatup') {
			$query = "SELECT * FROM commandsLog ORDER BY called_on DESC LIMIT 3";
			$result = mysql_query($query) or die(mysql_error());
			echo "well...<br />";
			while($row=mysql_fetch_assoc($result)) 
			{ 
				echo $row["called_on"];
				echo ": ";
				echo $row["command"];
				echo "<br />";
			}
		}
		return;
	}

	$safe_command = mysql_real_escape_string(strtolower($_REQUEST['command']));

	//Gem kommandoen
	save_command_call($safe_command);

	//Evaluer kommandoen.
	$query = "SELECT * FROM commands WHERE lower(command) = '".$safe_command."'";
	$result = mysql_query($query) or die(mysql_error());
	if (!$result) {
		$query = "SELECT * FROM commands WHERE lower(command) LIKE '".$safe_command."%'";
		$result = mysql_query($query) or die(mysql_error());
	}
	if($row = mysql_fetch_assoc($result)) {
		eval($row['code']);
	} else {
		echo get_message('cmd_not_found');
	}

}

function save_command_call($safe_command) {	

	$safe_command = htmlspecialchars($safe_command);	

	$sql = sprintf("INSERT INTO commandsLog (command, called_on, people_id) VALUES('%s', now(), %d )", mysql_real_escape_string($safe_command), mysql_real_escape_string(current_user()->id));
	
	mysql_query($sql) or die(mysql_error());
}

function fuck_off() {
	echo sprintf("Fuck off - '%s' er ikke en AJAX function", $_REQUEST['fkt']);
}

function wallanswer($inputstring)
{
	$query = "SELECT distinct Type_ FROM Answers";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_assoc($result)) 
	{ 
		$inputstring = strtoupper($inputstring);
		$sammenlignign[]=strtoupper($row["Type_"]);
	}
	$spørgsmålssammenlignign = array("HVAD","HVEM","HVOR","HVORDAN","?");


	$tok = strtok($inputstring, " \n\t.");

	while ($tok !== false) 
	{
	    if (in_array($tok,$sammenlignign))
	    { 
	    	$svarquery = "SELECT Answer FROM Answers where type_ like'".$tok."'order by rand() limit 1";
			$svarresult = mysql_query($svarquery) or die(mysql_error());
			$svar = mysql_fetch_assoc($svarresult);
			$tok = strtok($inputstring, " \n\t");
			return $svar["Answer"];
		} 
		if (in_array($tok,$spørgsmålssammenlignign)) 
		{
		    $svarquery = "SELECT Answer FROM Answers where type_ like'___Spørgsmål'order by rand() limit 1";
			$svarresult = mysql_query($svarquery) or die(mysql_error());
			$svar = mysql_fetch_assoc($svarresult);
			return $svar["Answer"];
		} 
		else
		{
	    $tok = strtok(" \n\t.");
		}
	}

	$randomsvarquery = "SELECT Answer FROM Answers where type_ like 'random' order by rand() limit 1";
	$randomsvarresult = mysql_query($randomsvarquery) or die(mysql_error());
	$randomsvar = mysql_fetch_assoc($randomsvarresult);
	return  $randomsvar["Answer"];
}

function define_command($command) {
	$command = stripslashes($_REQUEST['command']);
	$matches = array();
	if (!preg_match('/^define (?:"([^"]+)"|(\w+)) (.*)$/i', $command, $matches)) {
		echo "Du er en FEJL!";
		return;
	}

	$definitions = $matches[3];

	$command = strtolower($matches[1]);

	if (!$command)
		$command = strtolower($matches[2]);

	if (!$definitions) {
		echo "Du skal angive en betydning";
		return;
	}

	$query = "SELECT * FROM commands WHERE lower(command) = '".mysql_real_escape_string($command)."'";
	$result = mysql_query($query) or die(mysql_error());
	if($row = mysql_fetch_assoc($result)) {
		$sql = "UPDATE commands SET code = 'echo \'" . mysql_real_escape_string($definitions) . "\';' WHERE command = '{$row['command']}'";
		mysql_query($sql);
		echo "Kommando opdateret";
	} else {
		$sql = "INSERT INTO commands (command, code) VALUES('" . mysql_real_escape_string($command) . "', 'echo \'" . mysql_real_escape_string($definitions) . "\';')";
		mysql_query($sql);
		echo "Ny kommando oprettet";
	}
}

function write_to_IRC($written_by, $msg) {
	$irc_message = urlencode("$written_by sagde $msg");
	$irc_url = "http://sokr.dk/write_to_bacid12_irc.php?m=$irc_message";
	$ch = curl_init( $irc_url );
	 
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
		CURLOPT_POSTFIELDS => $irc_url
	);
	 
	curl_setopt_array( $ch, $options );
	 
	$result = json_decode(curl_exec($ch));

	return $result;
}


require_once('includes/db_close.php');

?>
