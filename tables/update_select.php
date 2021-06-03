<?php
    require "../POO/class_main_menu.php";
    require "../POO/class_connexion.php";
    require "../POO/class_manager_bd.php";
    require "function.php";
    require "request_prot_access.php";
?>
<?php
    include('../views/header.php');//j'inclus le header
?>
<?php
    
    $manager->update($_GET['num_acces'], $_GET['fields'], $_GET[$_GET['fields']], 'article', $_GET['fields']);#update in the database of the modified field (status or user)  
    if ($_GET['fields'] == 'user')//check that the change drop-down menu matches the user
    {
        if($_GET['valueStatusInitial'] == 'reject' OR $_GET['valueStatusInitial'] == 'processed' )//conditions for Processed and Rejected pages
        {
            $manager->update($_GET['num_acces'], 'status', 'tasks', 'article', 'status');#update of the status in tasks for the person collecting the article
        }
    }
    else //If the status drop-down menu is changed
    {
        if($_GET[$_GET['fields']] == 'tasks' AND $_GET['valueStatusInitial'] == 'undefined')//and if the status value is passed to tasks while on the undefined page
        {
            $list_prot_access = search_prot_access($_GET['num_acces']);//Recovering the prot access list
            $nb_prot_access = 0;
            if($list_prot_access != [])
            {
                $article = $manager->get('num_access', $_GET['num_acces'], 'article');
                foreach ($list_prot_access as $prot_access)
                {
                    if ($manager->get_exist_multiple('prot_access_table', 'id_article', $article['id_article'], 'prot_access',  $prot_access) == false)//Check if it does not already exist
                    {
                        $manager->add_prot_access('prot_access_table', $article['id_article'] , $prot_access);//adds the prot access found
                        $nb_prot_access++;
                    }
                } 
            }
            if ($nb_prot_access > 0)
            {
                echo "<div class='alert alert-info' role='alert'><br>" . $nb_prot_access . " protein accession numbers have been added<br></div>";//Returns the number of accession proteins added
            }
            
            
        }
    }
?>