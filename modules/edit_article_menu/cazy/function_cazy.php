<?php
//Recovering id_article from a pmid in the internal database
function recovery_id_article($pmid, $manager)
{
    $id_article_in_article = $manager->db->prepare("SELECT id_article FROM article WHERE num_access = $pmid");
    $id_article_in_article->execute();
    $id_article = $id_article_in_article->fetch();
    $id_article = $id_article['id_article'];
    return $id_article;
}
//Retrieval of protein accessions numbers in the internal database from the article_id
function recovery_prot_access_table($id_article, $manager)
{
    $array_prot_access= [];
    $request_prot_access_table = $manager->db->prepare("SELECT prot_access FROM prot_access_table WHERE id_article = '$id_article'");
    $request_prot_access_table->execute();
    $nb_line_request_prot_access_table = $request_prot_access_table->rowCount();
    if($nb_line_request_prot_access_table != 0)
    {
        while($info_prot_access_table = $request_prot_access_table->fetch())
        {
            $array_prot_access[] = $info_prot_access_table['prot_access'];
        }
    }
    return $array_prot_access;
}
//Recovery of the document id from the pmid in the cazy database
function recovery_document_id($pmid, $manager_cazy)
{
    $document_id_in_pub_document = $manager_cazy->pdo->prepare("SELECT document_id FROM extern_db.pub_document WHERE pub_db_acc = '$pmid'");
    $document_id_in_pub_document->execute();
    $document_id_find = $document_id_in_pub_document->fetch();
    $document_id = '';
    if (!empty($document_id_find))
    {
        $document_id = $document_id_find['document_id'];
    }
    return $document_id;
}
//Recovery of entry_id linked to our protein accessions numbers in the cazy database
function recovery_entry_id($array_prot_access, $manager_cazy)
{
    $dico_entry_id_prot_access = [];
    foreach($array_prot_access as $prot_access)
    {
        $request_entry_id_link_prot_access = $manager_cazy->pdo->prepare("SELECT entry_id FROM annotation WHERE db_acc = '$prot_access'");
        $request_entry_id_link_prot_access->execute();
        $nb_line_request_entry_id_link_prot_access= $request_entry_id_link_prot_access->rowCount();
        if($nb_line_request_entry_id_link_prot_access > 0)
        {
            $entry_id = $request_entry_id_link_prot_access->fetch();
            if(!array_key_exists($entry_id['entry_id'], $dico_entry_id_prot_access))
            {
                $dico_entry_id_prot_access[$entry_id['entry_id']] = array();
            }
            $dico_entry_id_prot_access[$entry_id['entry_id']][] = $prot_access;
            
        }
        
    }
    return $dico_entry_id_prot_access;  
}
//Check if a publication is linked to an entry_id in the cazy database. 
//If it is not the case put the link to add it
function check_entry_id_link_document_id($dico_entry_id_prot_access, $document_id, $pmid, $manager_cazy)
{
    $check_pmid = [];
    foreach($dico_entry_id_prot_access as $entry_id => $array_prot_access)
    {
        $request_annot_id = $manager_cazy->pdo->prepare("SELECT annot_id FROM annotation WHERE entry_id = $entry_id");
        $request_annot_id->execute();
        $annot_id = $request_annot_id->fetch();
        $annot_id = $annot_id['annot_id'];
        $request_entry_id_link_doc_id = $manager_cazy->pdo->prepare("SELECT entry_id FROM annotation INNER JOIN pub_annot ON annotation.annot_id = pub_annot.annot_id WHERE document_id = '$document_id' AND entry_id = $entry_id");
        //var_dump($request_entry_id_link_doc_id);
        $request_entry_id_link_doc_id-> execute();
        $check_doc_id_link = $request_entry_id_link_doc_id->fetch();
        $add_article = '<a href="http://10.1.22.212/privatesite/add_pubmed.cgi?entry_id=' . $entry_id . '&id=' . $pmid . '&annotid=' . $annot_id . '" target="_blank">ADD_article</a>';
        if(!empty($check_doc_id_link))
        {
            $check_pmid[$entry_id] = 'OK';
        }
        else
        {
            $check_pmid[$entry_id] = $add_article;
        }
        
    }
    return $check_pmid;
}
//Recovery of modules linked to the entry_id in the cazy database
function recovery_fam_acc($dico_entry_id_prot_access, $manager_cazy)
{
    $dico_fam_acc = [];
    foreach($dico_entry_id_prot_access as $entry_id => $array_prot_access)
    {
        $request_family_fam_acc = $manager_cazy->pdo->prepare("SELECT fam_acc FROM family INNER JOIN fam_composition ON family.fam_id = fam_composition.fam_id INNER JOIN entry_func ON fam_composition.fam_comp_id = entry_func.fam_comp_id WHERE entry_func.entry_id = $entry_id");
        $request_family_fam_acc->execute();
        $nb_line_request_family_fam_acc = $request_family_fam_acc->rowCount();
        if($nb_line_request_family_fam_acc > 0)
        {
            while($infos_family = $request_family_fam_acc->fetch())
            {
                if($infos_family['fam_acc'] != '')
                {
                    $dico_fam_acc[$entry_id][] = $infos_family['fam_acc'];
                }
            }
        }
    }
    return $dico_fam_acc;
}
//Recovery of ec_num linked to the entry_id in the cazy database
function recovery_ec_num($dico_entry_id_prot_access, $manager_cazy)
{
    $dico_ec_num = [];
    foreach($dico_entry_id_prot_access as $entry_id => $array_prot_access)
    {
        $request_function_ec_num = $manager_cazy->pdo->prepare("SELECT ec_num FROM function INNER JOIN entry_func ON function.function_id = entry_func.function_id WHERE entry_func.entry_id = $entry_id");
        $request_function_ec_num->execute();
        $nb_line_request_function_ec_num = $request_function_ec_num->rowCount();
        if($nb_line_request_function_ec_num > 0)
        {
            while($infos_function = $request_function_ec_num->fetch())
            {
                if($infos_function['ec_num'] != '')
                {
                    $dico_ec_num[$entry_id][] = $infos_function['ec_num'];
                }
            }
        }
    }
    return $dico_ec_num;
}
/*
Check if a document is linked to a function. 
/If it is OK, if the EC_NUM matches a particular pattern or add the link for linked. 
If not then we put N.A
*/
function recovery_check_function_pmid($dico_entry_id_prot_access, $document_id, $manager_cazy)
{
    $dico_check_pmid_function = [];
    foreach($dico_entry_id_prot_access as $entry_id => $array_prot_access)
    {
        $request_entry_func = $manager_cazy->pdo->prepare("SELECT function_id FROM entry_func WHERE entry_id = $entry_id");
        $request_entry_func->execute();
        $nb_line_request_entry_func = $request_entry_func->rowCount();
        if($nb_line_request_entry_func > 0)
        {
            while($infos_entry_func = $request_entry_func->fetch())
            {
                $request_check_pmid_function = $manager_cazy->pdo->prepare("SELECT * FROM pub_func INNER JOIN pub_annot USING(pub_annot_id) INNER JOIN annotation USING(annot_id) WHERE entry_id = $entry_id AND document_id = '$document_id' AND function_id = :function_id");
                $request_check_pmid_function->bindValue(":function_id", $infos_entry_func['function_id']);
                $request_check_pmid_function->execute();
                $nb_line_request_check_pmid_function = $request_check_pmid_function->rowCount();
                if($nb_line_request_check_pmid_function == 1)
                {
                    if(!array_key_exists($entry_id, $dico_check_pmid_function))
                    {
                        $dico_check_pmid_function[$entry_id] = 'OK';
                    }
                    else
                    {
                        $dico_check_pmid_function[$entry_id] .= '<br>' . 'OK';
                    }
                }
                else
                {
                    $request_ec_num_check =  $manager_cazy->pdo->prepare("SELECT ec_num FROM function WHERE function_id = :function_id");
                    $request_ec_num_check->bindValue(":function_id", $infos_entry_func['function_id']);
                    $request_ec_num_check->execute();
                    $ec_num_find = $request_ec_num_check->fetch();
                    $ec_num_find = $ec_num_find['ec_num'];
                    $regex = preg_match("/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/", $ec_num_find);
                    if ($regex == 1)
                    {
                        $lien_add_pmid_func = '<a href="http://10.1.22.212/privatesite/pub_func.cgi?edit=1&entry_id=' . $entry_id . '" target="_blank">Add_pub_func</a>';
                        if(!array_key_exists($entry_id, $dico_check_pmid_function))
                        {
                            $dico_check_pmid_function[$entry_id] = $lien_add_pmid_func;
                        }
                        else
                        {
                            $dico_check_pmid_function[$entry_id] .= '<br>' . $lien_add_pmid_func;
                        }
                    }
                    else
                    {
                        if(!array_key_exists($entry_id, $dico_check_pmid_function))
                        {
                            $dico_check_pmid_function[$entry_id] = 'N.A';
                        }
                        else
                        {
                            $dico_check_pmid_function[$entry_id] .= '<br>' . 'N.A';
                        }
                    }
                }
            }
        }
    }
    return $dico_check_pmid_function;

}

?>