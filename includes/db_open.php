<?php
require_once('includes/db_pass.php');

$conn = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("I cannot connect to the database because: " . mysql_error());

mysql_select_db($dbname) or die("Unable to select database cause: " . mysql_error());

mysql_set_charset('utf8',$conn);

//$query = "SELECT * FROM wall limit 1";
//$result = mysql_query($query) or die(mysql_error());
//printr(result)
?>