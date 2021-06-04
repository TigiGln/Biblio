<?php 
require "../../../POO/class_connexion.php";
require "../../../POO/class_manager_bd.php";
?>
<?php
    ob_start();
    include('../../../views/header.php');//j'inclus le header
    ob_end_clean();
?>
<?php
//function for adding the accession number to the database
function add_prot_access($num_access, $prot_access, $manager)
{
    if(!isset($num_access) AND !isset($prot_access))
    {
        http_response_code(400);//Bad request
    }
    else
    {
        $num_access = strip_tags($num_access);
        $prot_access = strip_tags($prot_access);
        $id_article_in_article = $manager->db->prepare("SELECT id_article FROM article WHERE num_access = '$num_access'");
        $id_article_in_article->execute();
        $id_article = $id_article_in_article->fetch();
        $id_article = $id_article['id_article'];
        if ($manager->get_exist_multiple('prot_access_table', 'id_article', $id_article, 'prot_access',  $prot_access) == false)
        {
            $valid = $manager->add_prot_access('prot_access_table', $id_article, $prot_access, null);
            if($valid != "PDO::errorInfo():")
            {
                echo 'add prot_access';
            }
            else
            {
                echo "the accession number could not be added due to an error"; 
            }

        }
        else
        {
            echo "The accession number already exists"; 
        }
        
    }
}
//fonction de suppression du numéro d'accession dans la base de données
function delete_prot_access($prot_access, $manager)
{
    if(!isset($prot_access))
    {
        http_response_code(400);//Bad request
    }
    else
    {
        $prot_access = strip_tags($prot_access);
        $delete_prot_access = $manager->db->prepare("DELETE FROM `prot_access_table` WHERE prot_access = '$prot_access'");
        $delete_prot_access->execute();
        if($delete_prot_access)
        {
            echo 'prot_access delete';
        }
        else
        {
            echo "the accession number could not be deleted due to an error"; 
        }

        
    }
}
if ($_GET['function'] == 'add')
{
    add_prot_access($_GET['num_access'], $_GET['prot_access'], $manager);
}
else
{
    delete_prot_access($_GET['prot_access'], $manager);
}
