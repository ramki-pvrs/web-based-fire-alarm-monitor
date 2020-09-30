<?php
//DB connection string and username/password
$connStr = 'mysql:host=localhost;dbname=hootus';
$user = 'root';
$pass = 'root123';


//create the connection object
try
{
	//connect to the database
	$con = new PDO($connStr, $user, $pass);
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$con->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
	$con->setAttribute(PDO::ATTR_AUTOCOMMIT,TRUE);
	$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
	//print_r("dbconnect success");
}
catch(PDOException $e)
{
	showHeader('Error');
	showError("Sorry, an error has occurred.Please try your request later\n" . $e->getMessage());
}

$setGrpConcat = $con->prepare("SET group_concat_max_len=1000000;");
$setGrpConcat->execute();
//https://stackoverflow.com/questions/23921117/disable-only-full-group-by
$setOnlyGroupBy = $con->prepare("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
$setOnlyGroupBy->execute();
?>
