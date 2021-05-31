<?php
//query function on pubmed to search for protein accession numbers
function search_prot_access($pmid)
{
    #retrieving the webenv for the pubmed database for a list of pmid
    $base = 'https://www.ncbi.nlm.nih.gov/entrez/eutils/';
    $search = 'esearch.fcgi?db=Pubmed&usehistory=y&term=';
    $url1 = $base . $search . $pmid;
    $output1 = file_get_contents($url1);
    $result1 = new SimpleXMLElement($output1);//xml parsing object creation
    $web_env1 = $result1->WebEnv;
    $key1 = $result1->QueryKey;

    #retrieving the webenv for the protein database
    $search2 = 'elink.fcgi?dbfrom=pubmed&db=protein&query_key=';
    $url2 = $base . $search2 . $key1 . '&WebEnv=' . $web_env1 . '&linkname=pubmed_protein&cmd=neighbor_history';
    //echo $url2 ."<br>";
    $output2 = file_get_contents($url2);
    $result2 = new SimpleXMLElement($output2);
    $web_env2 = $result2->LinkSet->WebEnv;
    $key2 = $result2->LinkSet->LinkSetDbHistory->QueryKey;

    #retrieval of accession numbers if available
    $search3 ='esummary.fcgi?db=protein&query_key=';
    $url3 = $base . $search3 . $key2 . '&WebEnv=' . $web_env2;
    $output3 = file_get_contents($url3);
    $result3 = new SimpleXMLElement($output3);
    $num_accession_prot = [];
    if (!empty($result3->DocSum))
    {
        foreach($result3->DocSum as $docsum)
        {
            foreach($docsum->Item as $item)
            {
                if ($item["Name"] == "AccessionVersion")
                {
                    $num_accession_prot[] .= strval($item[0]);
                }
            }
           
        }  
    }
    return $num_accession_prot;
}

//function returning an associative array having as key the accession number (pmid) and as values the list of associated protein accessions numbers
function num_access_associated($listpmid)
{
    $dico_num_access_prot = [];
    for($i= 0; $i<count($listpmid); $i++)
    {
        $num_access_prot = [];
        $pmid = trim($listpmid[$i]);
        $num_access_prot = search_num_access($pmid);
        if (!empty($num_access_prot))
        {
            $dico_num_access_prot[$pmid] = $num_access_prot;
        }
    }
    return $dico_num_access_prot;
}
?>