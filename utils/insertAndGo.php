<?php
/*
 * Created on Fri May 7 2021
 * Latest update on Mon May 17 2021
 * Info - script that will link or add and appropriate an article.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */

//CLASS IMPORT
require('../POO/class_article.php');
require('../insertion/request.php');
require('../views/header.php');
?>
<?php
if(isset($_GET['ORIGIN']) && isset($_GET['ID'])) {
    switch($_GET['ORIGIN']) {
        case 'pubmed':
            insertAndGoPubmed($manager);
            break;
        default:
            break;
    }
}

function insertAndGoPubmed($manager) {
    $search = new SimpleXMLElement(file_get_contents('https://www.ncbi.nlm.nih.gov/pmc/utils/idconv/v1.0/?ids=PMC'.$_GET['ID']));
    $result = search(array($search->record->attributes()->pmid), 0);
    $list_info = recovery($result);
    if (!empty($list_info)) {
        $res = 1;
        if(!$manager->get_exist("article", "num_access", $_GET['ID'])) {
            $num_access = $list_info[0];
            $doi = $list_info[1];
            $pmcid = $list_info[2];
            $title = $list_info[3];
            $year = $list_info[4];
            $abstract = $list_info[5];
            $authors = $list_info[6];
            $journal = $list_info[7];
            $listauthors = $list_info[8];
            $origin = 'pubmed';
            $object_article = new Article($origin, $num_access, $title, $abstract, $year, $journal, $pmcid, '2', $listauthors, $_SESSION["userID"]);
            $res = $manager->add($object_article);
        } else { header('Location: ../tools/readArticle.php?NUMACCESS='.$num_access."&ORIGIN=pubmed"); }
        if($res) { header('Location: ../tools/readArticle.php?NUMACCESS='.$num_access."&ORIGIN=pubmed"); }
        else { echo '<div class="alert alert-danger" role="alert">An error occured. Please retry.</div>'; }
    }
}
?>