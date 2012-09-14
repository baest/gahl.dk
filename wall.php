<?php

$spanien = "Jeg kommer fra Spanien.";
echo "INPUT: ".$spanien."<br>";
$svar = wallanswer($spanien);
echo "Svarer: ".$svar;

function wallanswer($inputstring)
{

$dbhost = "mydb7.surftown.dk";
$dbname = "sorenk1_gahl";
$dbuser = "sorenk1_safran";
$dbpass = "lunner";

$conn=mysql_connect ($dbhost, $dbuser, $dbpass) or die ("I cannot connect to the database because: " . mysql_error());
mysql_select_db($dbname) or die("Unable to select database cause: " . mysql_error());



$query = "SELECT distinct Type_ FROM sorenk1_gahl.Answers";
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
    	$svarquery = "SELECT Answer FROM sorenk1_gahl.Answers where type_ like'".$tok."'order by rand() limit 1";
		$svarresult = mysql_query($svarquery) or die(mysql_error());
		$svar = mysql_fetch_assoc($svarresult);
		$tok = strtok($inputstring, " \n\t");
		return $svar["Answer"];
	} 
	if (in_array($tok,$spørgsmålssammenlignign)) 
	{
	    $svarquery = "SELECT Answer FROM sorenk1_gahl.Answers where type_ like'___Spørgsmål'order by rand() limit 1";
		$svarresult = mysql_query($svarquery) or die(mysql_error());
		$svar = mysql_fetch_assoc($svarresult);
		return $svar["Answer"];
	} 
	else
	{
    $tok = strtok(" \n\t.");
	}
}

$randomsvarquery = "SELECT Answer FROM sorenk1_gahl.Answers where type_ like 'random' order by rand() limit 1";
$randomsvarresult = mysql_query($randomsvarquery) or die(mysql_error());
$randomsvar = mysql_fetch_assoc($randomsvarresult);
return  $randomsvar["Answer"];
}
?> 
