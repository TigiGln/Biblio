<?php
    require "../../modules/edit_article_menu/cazy/function_cazy.php";
    ob_start();
    include('../../views/header.php');//j'inclus le header
    ob_end_clean();
?>
<?php
function generic_suppression($table, $column, $value, $manager)
{
    $delete = $manager->db->prepare("DELETE FROM $table WHERE $column = $value");
    $delete->execute();
    if($delete)
    {
        return 'success delete';
    }
    else
    {
        return "PDO::errorInfo():";
        print_r($manager->db->errorInfo());
    }
}
function delete_article($num_access, $manager)
{
    if(!isset($num_access))
    {
        http_response_code(400);//Bad request
    }
    else
    {
        $num_access = strip_tags($num_access);
        $id_article = recovery_id_article($num_access, $manager);
        $delete_link_author = generic_suppression('author_article', 'id_article', $id_article, $manager);
        if($delete_link_author == 'success delete')
        {
            $delete_prot_acess_associé = generic_suppression('prot_access_table', 'id_article', $id_article, $manager);
            if($delete_prot_acess_associé == 'success delete')
            {
                $delete_general_note = generic_suppression('general_note', 'id_article', $id_article, $manager);
                if($delete_general_note == 'success delete')
                {
                    $delete_note = generic_suppression('note', 'id_article', $id_article, $manager);
                    if($delete_note == 'success delete')
                    {
                        $delete_article = generic_suppression('article', 'id_article', $id_article, $manager);
                        if($delete_article == 'success delete')
                        {
                            echo 'article delete';
                        } 
                        else
                        {
                           echo "the article could not be deleted due to an error";
                        }
                    } 
                }
            }  
        } 
    }
}
delete_article($_GET['num_access'], $manager);
?>