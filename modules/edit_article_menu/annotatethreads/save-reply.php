<?php
	/*
	* Created on Mon May 3 2021
	* Latest update on Tue May 11 2021
	* Info - JS for annotate threads module in edit article menu
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/

	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");
	
	/* Parse Request Parameters */
	$file = "./replies.xml";
	$xml = simplexml_load_file($file);
	$ID = $_POST['ORIGIN'].'_'.$_POST['ID'];
	$user = $_SESSION['username'];
	$text = $_POST["text"];
	$date = (new DateTime())->format('Y-m-d-h-i-s');
	$tag = "author";

	/* Handle Reply Saving */
	$datas = array($ID, array(array($tag, "name", $user), array(array("date", $date), array("content", rawurlencode($text)))));
	$save = new SaveLoadStrategies("../../../", $manager);
	http_response_code($save->saveAsXML($file, $datas, false));
?>