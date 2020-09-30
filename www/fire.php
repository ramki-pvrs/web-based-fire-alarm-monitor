<?php 
	session_start();
	include dirname(__FILE__).'/device2db.php';
	if(isset($_POST['did']) && isset($_POST['dname']) && isset($_POST['sts'])){
		$panelID = filter_input(INPUT_POST, 'did', FILTER_VALIDATE_INT);
		$deviceName = filter_input(INPUT_POST, 'dname');
		$alarm = filter_input(INPUT_POST, 'sts');
		//alarm update is not done if admin as made the location inactive status=-1
		$sql="UPDATE hootus.location SET lastAlarm=".$alarm.", status=1, lastStatusUpdate=CURRENT_TIMESTAMP WHERE panelID=".$panelID." AND status != -1;";
		//echo($sql);
		$sql=$con->prepare($sql);
		$con->beginTransaction(); 
		$sql->execute();
		$con->commit();
	}
	//curl -d "did=6001&dname=IELEKTRON&sts=1" -X POST http://localhost:80/fire.php
?>