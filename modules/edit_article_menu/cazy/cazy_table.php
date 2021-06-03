<?php
require "../../../POO/manager_cazy.class.php";
require "./function_cazy.php";
$manager_cazy = new ManagerCazy('10.1.22.207', 'cazy_7', 'glyco', 'Horror3');
if(isset($_GET['body']))
{
	include('../../../views/header.php');
	echo "<body onload='load_lien()'>";
	echo "<div class='flex p-4 w-100 overflow-auto' style='height: 100vh;'>";
	echo "<p id='add_prot_access' style='display:inline; margin-bottom:0px;'></p>";
	echo '<div id="cazy" class="d-flex flex-column" data-article="[ID]">';
}
else
{
	ob_start(); //Pour ne pas afficher le header dans la reponse
	include('../../../views/header.php');
	ob_end_clean();
}
?>
<?php
    if(!isset($_GET["NUMACCESS"]))
    {
        http_response_code(400);//Bad request
    }
    else
    {
        $pmid = $_GET["NUMACCESS"];
        //$pmid = '28329766';
        //$pmid = '33046698';
        //$pmid = '32338906';
        //Récupération de l'id_article grâce au pmid dans la base interne
        $id_article = recovery_id_article($pmid, $manager);
        //Récupération du document_id grâce au pmid dans la base cazy
        $document_id = recovery_document_id($pmid, $manager_cazy);
        if(!empty($id_article))
        {
            echo '<form method="post" action = "#" enctype="multipart/form-data" >';
            echo "<input type='text' id= 'input_prot_access' placeholder='Please Enter prot_access' oninput= 'listen_input(this)'>";
            echo "<input type='button' id='add_button' value='Add' onclick='click_add()'>";
            echo "<a href='#footer' style='float: right;'> Bottom Page</a>";
            echo "<a id='header'></a>";
            echo '<table class="table table-responsive table-hover table-bordered" id="table_cazy">';
            echo "<thead><tr class='table-info'><th class='sort_column'>Num Accession</th><th class='sort_column'>Entry ID Cazy</th><th class='sort_column'>check_pmid</th><th class ='sort_column'>check_func</th><th class='sort_column'>check_pmid_func</th></tr></thead><tbody>";
            //Récupération des prot_access grâce à l'id_article dans la base interne
            $array_prot_access = recovery_prot_access_table($id_article, $manager);
            if(!empty($array_prot_access))
            {
                
                //Récupération des entry_id lié à nos prot_access dans la base cazy
                $dico_entry_id_prot_access = recovery_entry_id($array_prot_access, $manager_cazy);
                /*echo "<pre>";
                var_dump($dico_entry_id_prot_access);
                echo "</pre>";*/
                //Différence enetre deux array
                $array_cazy = [];
                foreach($dico_entry_id_prot_access as $array_prot_access_cazy)
                {
                    $array_cazy = array_merge($array_cazy, $array_prot_access_cazy);
                }
                /*echo "<pre>";
                var_dump($array_cazy);
                echo "</pre>";*/
                $array_diff = array_diff($array_prot_access, $array_cazy);
                /*echo "<pre>";
                var_dump($array_diff);
                echo "</pre>";*/
                if(!empty($array_diff))
                {
                    
                    foreach($array_diff as $prot_access)
                    {
                        $delete = "<input type=button id=input_" . $prot_access . " value=Del onclick='click_delete(this)'>";
                        $add_prot_access = '<a href="http://10.1.22.212/privatesite/new_entry.cgi?db_acc=' . $prot_access . '&fetch=1" target="_blank">ADD db_acc</a>';
                        echo "<tr id=line_" . $prot_access . "><td>" . $prot_access . ' ' . $delete . "</td><td>$add_prot_access</td><td></td><td></td><td></td></tr>";
                    }
                }
                if(count($array_diff) != count($array_prot_access))
                {
                    if(!empty($dico_entry_id_prot_access))
                    {
                        /*echo "<pre>";
                        var_dump($dico_entry_id_prot_access);
                        echo "</pre>"; */  
                        //Récupération du lien pmid lié à une entry_id dans la base cazy
                        $dico_check_pmid = check_entry_id_link_document_id($dico_entry_id_prot_access, $document_id, $pmid, $manager_cazy);
                        //var_dump($dico_check_pmid);
                        //Récupération du nom du module pour chaque 
                        $dico_fam_acc = recovery_fam_acc($dico_entry_id_prot_access, $manager_cazy);
                        /*echo "<pre>";
                        var_dump($dico_fam_acc);
                        echo "</pre>";*/
                        $dico_ec_num = recovery_ec_num($dico_entry_id_prot_access, $manager_cazy);
                        /*echo "<pre>";
                        var_dump($dico_ec_num);
                        echo "</pre>";*/
                        $dico_check_pmid_function = recovery_check_function_pmid($dico_entry_id_prot_access, $document_id, $manager_cazy);
                        /*echo "<pre>";
                        var_dump($dico_check_pmid_function);
                        echo "</pre>";*/
                        foreach($dico_entry_id_prot_access as $entry_id => $array_prot_access)
                        {
                            $name_prot_access = '';
                            echo "<tr id=line_" . $entry_id . "><td>";
                            for($i=0; $i<count($array_prot_access); $i++)
                            {
                                $delete = "<input type=button id=input_" . $array_prot_access[$i] . " value=Del onclick='click_delete(this)'>";
                                echo  $array_prot_access[$i] . ' '. $delete . '<br>';
                                $name_prot_access =  $array_prot_access[$i];  
                            }
                            echo "</td><td>" . $entry_id . "</td><td>" . $dico_check_pmid[$entry_id] . "</td><td>";
                            if(!empty($dico_fam_acc) AND !empty($dico_ec_num))
                            {
                                for($i=0; $i<count($dico_fam_acc[$entry_id]); $i++)
                                {
                                    echo  $dico_fam_acc[$entry_id][$i] . '/' . $dico_ec_num[$entry_id][$i] . '<br>';     
                                }
                            }
                            else if(empty($dico_fam_acc) AND empty($dico_ec_num))
                            {
                                $input_ec_num = "<input type='text' oninput='add_ec_num(this)' class='ec_num' id='" . $name_prot_access ."' placeholder='Enter ec_num' size='10'>";
                                $lien_add_func = '<a class= "lien_add_func" href="http://10.1.22.212/privatesite/add_entryfunct.cgi?entry_id=' . $entry_id . '&edit=1&ec_num=" target="_blank">Add_func</a>';
                                echo $lien_add_func . ' ' . $input_ec_num;
                            }
                            else if(empty($dico_fam_acc) AND !empty($dico_ec_num))
                            {
                                for($i=0; $i<count($dico_ec_num[$entry_id]); $i++)
                                {
                                    echo 'No module' . '/' . $dico_ec_num[$entry_id][$i] . '<br>';
                                }
                            }
                            else
                            {
                                for($i=0; $i<count($dico_fam_acc[$entry_id]); $i++)
                                {
                                    echo  $dico_fam_acc[$entry_id][$i] . '/' . 'No EC_num' . '<br>';
                                        
                                }
                            }
                            echo "</td><td>";
                            if(!empty($dico_check_pmid_function))
                            {
                                echo $dico_check_pmid_function[$entry_id];
                            }
                            echo "</td></tr>";
                        }
                    }
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</form>';
            echo "<a id='footer'></a>";
            echo "<a href='#header' style='float:right;'>Top Page</a>";
        }
    }
?>
<?php 
if(isset($_GET['body']) AND $_GET['body'] == '1')
{
	include($position . '/views/footer.php');//inclusion du pied de page
}
else
{
	echo '<script src="' . $position . '/Biblio/tables/table_sort.js" async="true"></script>';
}   
    
?>