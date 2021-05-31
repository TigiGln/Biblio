<?php
	/*
	* Created on Tue Apr 21 2021
	* Latest update on Thu May 20 2021
	* Info - PHP for notes module in edit article menu
	* @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
	*/

	$position = "../../..";
	require($position.'/views/dbLoader.php');
	require($position."/POO/class_saveload_strategies.php");

	/* Parse Request Parameters */
	$file = "./notes.xml";
	$ID = $_GET['ORIGIN']."_".$_GET['ID'];
	$user = $_SESSION['username'];
	$tag = "author";
	$sep = ";";
	$subSep = ',';
	/* Prepare Header */
	header("Content-type: text/plain");

	/* Handle Notes Loadings */
	$load = new SaveLoadStrategies("../../../", $manager);
	$res = $load->loadAsXML($file, $ID, $tag, $user);
	if($res == 404) 
	{ 
		http_response_code(404); 
	}
	else if($res == 400) 
	{ 
		http_response_code(400); 
	}
	else 
	{
		http_response_code(200);
		echo $res;
	}
?>