<?php
//function that makes a request to pubmed to retrieve the pmcid based on a pmid
function search_pmcid($pmid)
{
    $base = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&usehistory=y&term=';
    $search = file_get_contents($base.$pmid);
    $search = new SimpleXMLElement($search);//création de l'objet de parsing xml
    $web_env = $search->WebEnv;//récupération de la valeur du web Env pour la requête suivante
    $base1 = "http://www.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?rettype=abstract&retmode=xml&db=Pubmed&query_key=1&WebEnv=" . $web_env;
    $url = $base1 . "&usehistory=y&term=" . $pmid;
    $output = file_get_contents($url);//requête pour récupérer le xml des données importantes de l'article
    $output1 = new SimpleXMLElement($output);
    if (!empty($output1->PubmedArticle->MedlineCitation->Article->AuthorList->Author))
    {
        $pmcid ='';
        foreach($output1->PubmedArticle->PubmedData->ArticleIdList->ArticleId as $elem)
        {
            $regex2 = preg_match('/^PMC[0-9]+$/', $elem);
            if($regex2 == 1)
            {
                $pmcid = strval($elem);
            }
        }
    }
    return $pmcid;
}



?>