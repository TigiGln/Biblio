<?php
	/*
	* Created on Fri Apr 23 2021
	* Latest update on Tue May 11 2021
	* Info - PHP for send module in edit article menu
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/
	
	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");

	$userID = $_SESSION['userID']; 
	$newUserID = $_POST["newUser"];
	$ORIGIN = $_POST["ORIGIN"];
	$ID = $_POST["ID"];

	$saveload = new SaveLoadStrategies("../../../", $manager);
	if(!$saveload->checkAsDB("article", array("num_access"), array(array("origin", $ORIGIN), array("num_access", $ID), array("user", $userID)))) { http_response_code(404); }
	else if($newUserID != "null" && !$saveload->checkAsDB("user", array("id_user"), array(array("id_user", $newUserID)))) { http_response_code(404); }
	else {
		$cols = array();
		array_push($cols, array("user", $newUserID));
		$conditions = array();
		array_push($conditions, array("num_access", $ID), array("origin", $ORIGIN));
		http_response_code($saveload->saveAsDB("article", $cols, $conditions, true));
	}
	echo http_response_code();
?>