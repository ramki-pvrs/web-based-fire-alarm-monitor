<?php 
	session_start();
	include dirname(__FILE__).'/device2db.php';
	//var_dump($_POST);
	if(isset($_POST['lastAlarm'])){
		$alarm = filter_input(INPUT_POST, 'lastAlarm');
		//alarm update is not done if admin as made the location inactive status=-1
		$sql="UPDATE hootus.location SET lastAlarm=".$alarm.", status=1, lastStatusUpdate = CURRENT_TIMESTAMP WHERE id BETWEEN 1 AND 300;";
		//header("Content-Type: application/json; charset=utf-8");
  		//echo json_encode("successful");
		$sql=$con->prepare($sql);
		$con->beginTransaction(); 
		$sql->execute();
		$con->commit();
	}
	//curl -d "did=6001&dname=IELEKTRON&sts=1" -X POST http://localhost:80/fire.php
?>