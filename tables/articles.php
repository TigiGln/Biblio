<?php
    //Import of files necessary for the good functioning of the table pages
    require "../POO/class_main_menu.php";
    require "function.php";
?>
<?php
    include('../views/header.php');//inclusion de l'entête
    $menu = new MainMenu($_GET['status'], $manager);//creation du menu
    $menu->write();//ecriture des éléments du menu
?>
    <div class="flex p-4 w-100 overflow-auto" style="height: 100vh;">
        <?php
            echo '<form method="post" action = "update_pmcid.php" enctype="multipart/form-data" >';//added a form to go and update pmcid
            echo "<p id='info_change'><p>";//Paragraph to inform that the changes have been taken into account

            $_SESSION['status_page'] = $_GET["status"];#Store the page status in a session variable for transmission between pages
            $list_status_initial = search_table_status($_GET["status"], $_SESSION['username'], $manager);#display of the correct table page according to status and user
            echo '</form>';
        ?>
    </div>
<?php     
    include('../views/footer.php');//inclusion du pied de page
?>
