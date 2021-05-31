<?php
	/*
	* Created on Wed Apr 28 2021
	* Latest update on Tue May 11 2021
	* Info - PHP for grade module in edit article menu
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/
	
	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");

	$ID = $_POST['ID'];
	$GRADE = $_POST['GRADE'];

	$saveload = new SaveLoadStrategies("../../../", $manager);
	$cols = array();
	array_push($cols, "*");
	$conditions = array();
	array_push($conditions, array("id_article", $ID), array("id_user", $_SESSION['userID']));
	$doExist = $saveload->checkAsDB("note", $cols, $conditions);
	if($doExist) {
		$cols = array();
		array_push($cols, array("note", $GRADE));
		http_response_code($saveload->saveAsDB("note", $cols, $conditions, true));
	} else {
		$cols = array();
		array_push($cols, array("id_article", $ID), array("id_user", $_SESSION['userID']), array("note", $GRADE));
		$conditions = array();
		http_response_code($saveload->saveAsDB("note", $cols, $conditions, false));
	}
	echo http_response_code();	
?>