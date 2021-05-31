<script>
	//Creation of the different popover
	function refreshPopovers() 
	{
		  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
		  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
			return new bootstrap.Popover(popoverTriggerEl);
		  });
	}
	refreshPopovers()
</script>
<?php
	//Adding JavaScript scripts depending on where you are in the software
	if($_SERVER["SCRIPT_NAME"] == "/Biblio/insertion/result.php")
	{
		echo "<script src='" . $position . "/insertion/update_request_table.js'></script>";
	}
	elseif($_SERVER["SCRIPT_NAME"]== "/Biblio/tables/articles.php")
	{
		echo '<script src="' .  $position . '/tables/update_select.js"></script>';
	}
	if ($_SERVER["SCRIPT_NAME"] == "/Biblio/insertion/result.php" || $_SERVER["SCRIPT_NAME"] == "/Biblio/tables/articles.php" || $_SERVER["SCRIPT_NAME"] == "/Biblio/modules/edit_article_menu/cazy/cazy_table.php")
	{
		echo '<script src="' . $position . '/tables/table_sort.js"></script>';
	}
	if ($_SERVER["SCRIPT_NAME"] == "/Biblio/tools/readArticle.php" || $_SERVER["SCRIPT_NAME"] == "/Biblio/modules/edit_article_menu/cazy/cazy_table.php")
	{
		echo "<script src='" . $position . "/modules/edit_article_menu/cazy/gestion_cazy.js' async></script>";
	}
	/*if ($_SERVER["SCRIPT_NAME"] == "/Biblio/tools/readArticle.php")
	{
		echo '<script src="' . $position . '/tables/table_sort.js" async></script>';
	}*/
?>
</body>
</html>