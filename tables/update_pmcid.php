<?php
    include('../views/header.php');//inclusion de l'entête
?>
<?php
//script to update the pmcid of the user's items with the status tasks
require "./search_pmcid.php";
//Recovering the pmid of items that do not have a PMCID but are owned by the user and have the status tasks.
$request_num_access = $manager->db->prepare("SELECT num_access FROM article WHERE pmcid = '' AND status = '2' AND user = :user");
$request_num_access->bindValue(':user', $_SESSION['userID']);
$request_num_access->execute();
$nb_line = $request_num_access->rowCount();
if($nb_line > 0)
{
    $list_num_access = [];
    while($num_access_find = $request_num_access->fetch())
    {
        $list_num_access[] = $num_access_find['num_access'];//Recovering the pmid list
    }
    foreach($list_num_access as $num_access)
    {
        $pmcid_finc = search_pmcid($num_access);
        if ($pmcid_finc != '')
        {
            $manager->db->query("UPDATE article SET pmcid = '$pmcid_finc' WHERE num_access = $num_access");//Update for everyone with a change
            $line_table["pmcid"] = $pmcid_finc;
        }
    }

    
}
header("Location: ./articles.php?status=tasks");//redirection to the user's tasks table page during a session

?>