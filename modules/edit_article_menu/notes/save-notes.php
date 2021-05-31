<?php
	/*
	* Created on Tue Apr 21 2021
	* Latest update on Tue May 11 2021
	* Info - PHP for notes module in edit article menu
	* Info - SAVE NOTES AS FOLLOWING: <IDXXX> -> <author "name"> -> date, content
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/

	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");

	/* Parse Request Parameters */
	$file = "./notes.xml";
	$ID = $_POST['ORIGIN']."_".$_POST['ID'];
	$content = $_POST["notes"];
	$date = (new DateTime())->format('Y-m-d');
	$user = $_SESSION['username'];
	$tag = "author";
	
	/* Handle Notes Saving */
	$datas = array($ID, array(array("author", "name", $user), array(array("date", $date), array("content", rawurlencode($content)))));
	$save = new SaveLoadStrategies("../../../", $manager);
	http_response_code($save->saveAsXML("./notes.xml", $datas, true));
?>

