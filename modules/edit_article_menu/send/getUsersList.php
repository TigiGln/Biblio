<?php
	/*
	* Created on Tue Apr 27 2021
	* Latest update on Tue May 11 2021
	* Info - PHP for send module in edit article menu
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/
	
	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");

	$saveload = new SaveLoadStrategies("../../../", $manager);
	$cols = array();
	array_push($cols, "id_user", "name_user", "email", "profile");
	$conditions = array();
	
	$res = $saveload->loadAsDB("user", $cols, $conditions, null);
	if(empty($res)) { http_response_code(404); }
	else {
	echo json_encode($res); 
	http_response_code(200);
	}
?>