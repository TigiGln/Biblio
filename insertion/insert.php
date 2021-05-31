<?php
    
    require "../POO/class_main_menu.php";
    require "../POO/class_connexion.php";
    require "../POO/class_manager_bd.php";
    require "../POO/class_article.php";
    require "request.php";
?>
<?php
    include('../views/header.php');
?>
<div class="flex p-4 w-100 overflow-auto" style="height: 100vh;">
<?php
    
    if ($_SESSION != [])#Checking that the session is in progress
    {
        $list_articles = $_SESSION["list_articles"];//Retrieving the article list as objects
        if (isset($_POST["check"]) AND $_POST["check"] != [])
        {
            foreach ($_POST["check"] as $value)//Loop on selected lines
            {
                $manager->add($list_articles[$value]);//Adding items to the database according to the selected lines
                $manager->add_authors($list_articles[$value]);//Adding the authors linked to each article in the author table
                $manager->add_link_authors_article($list_articles[$value], $value);//insertion of the link between the article table and the author table with a specific table
                //echo '<div class="alert alert-info" role="alert">Article N°'. $value .' was successfully added to the database</div>';

            }
        }
        
        
    }
    header("Location: ../tables/articles.php?status=undefined")
?>
</div>
<?php      
    include('../views/footer.php');
?>