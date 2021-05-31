<?php
//function to retrieve the xml file of the article
function search($listpmid, $i)
{
    $id = trim($listpmid[$i]);
    $base = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&usehistory=y&term=';
    $search = file_get_contents($base.$id);
    $search = new SimpleXMLElement($search);//creation of the xml parsing object
    $web_env = $search->WebEnv;//retrieve the value of the web Env for the following query
    $base1 = "http://www.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?rettype=abstract&retmode=xml&db=Pubmed&query_key=1&WebEnv=" . $web_env;
    $url = $base1 . "&usehistory=y&term=" . $id;
    $output = file_get_contents($url);//query to retrieve the xml of important data from the article
    
    return $output;
}
//recovery of important elements of the article if the abstract and authors are mentioned
function recovery($output)
{
    $output1 = new SimpleXMLElement($output);//creation of the xml parsing object
    //Checking the article to see if there are authors available.
    if (!empty($output1->PubmedArticle->MedlineCitation->Article->AuthorList->Author))
    {
        //if this is the case recovery of important elements 
        $pmid = strval($output1->PubmedArticle->PubmedData->ArticleIdList->ArticleId[0]);
        $doi = '';
        $pmcid ='';
        foreach($output1->PubmedArticle->PubmedData->ArticleIdList->ArticleId as $elem)
        {
            $regex = preg_match('/^10\.[0-9]{4}\//', $elem);
            if ($regex == 1)
            {
                $doi = strval($elem);
            }
            $regex2 = preg_match('/^PMC[0-9]+$/', $elem);
            if($regex2 == 1)
            {
                $pmcid = strval($elem);
            }
        }
        $title = strval($output1->PubmedArticle->MedlineCitation->Article->ArticleTitle);
        if (isset($output1->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year))
        {
            $year = strval($output1->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->Year);
        }
        else
        {
            $year = strval($output1->PubmedArticle->MedlineCitation->Article->Journal->JournalIssue->PubDate->MedlineDate);
            $year = substr($year, 0, 4);

        }
        $abstract = "";
        $abstract_no_empty= $output1->PubmedArticle->MedlineCitation->Article->{'Abstract'}->AbstractText;
        if (!empty($abstract_no_empty))
        {
            foreach ($abstract_no_empty as $charac)
                {
                    $abstract .= strval($charac);
                }
        }
        else
        {
            $abstract = "No abstract available";
        }
        $authors = [];
        $authorsList = [];
        $authors_no_empty = $output1->PubmedArticle->MedlineCitation->Article->AuthorList->Author;
        if (!empty($authors_no_empty))
        {
            foreach ($authors_no_empty as $name)
            {
                $authors[] = strval($name->LastName) . " " . strval($name->Initials);
                $authorsList[] = strval($name->LastName) . " " . strval($name->ForeName);
            }  
        }
        $journal = strval($output1->PubmedArticle->MedlineCitation->Article->Journal->Title);
        $liste_info = [$pmid, $doi, $pmcid, $title, $year, $abstract, $authors, $journal, $authorsList];
        return $liste_info;//returns the important parser elements in a list
    }
    
}
 
?>