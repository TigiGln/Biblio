<?php
    
    require "../POO/class_main_menu.php";
    require "../POO/class_connexion.php";
    require "../POO/class_manager_bd.php";
    require "../POO/class_article.php";
    require "../POO/manager_cazy.class.php";
    require "request.php";
?>
<?php
    include('../views/header.php'); //import header
    $menu = new MainMenu('Insertion', $manager);//Instantiation du menu
    $menu->write();
    $manager_cazy = new ManagerCazy('10.1.22.207', 'extern_db', 'glyco', 'Horror3');//instantiation of the cazy manager
?>
<div class='flex p-4 w-100 overflow-auto' style='height: 100vh;'>
    <form method="post" action="insert.php" enctype="multipart/form-data">
        <?php
            //creation of the list to be checked by JavaScript to avoid duplication
            $list_num_access_bd = $manager->get_num_access('num_access', 'article');//query the database to retrieve the present access_numbers
            $list_num_access_cazy = $manager_cazy->get_numAccess('pub_db_acc', 'pub_document');//request to recover cazy's pmid
            $list_num_access_already_present = array_values(array_unique(array_merge($list_num_access_bd, $list_num_access_cazy)));//creation of a list of unduplicated pmids that are in both databases
            $pmid = "";
            $listpmid = [];
            $list_objects = [];
            if (isset($_POST["textarea"]) AND $_POST["textarea"] != "")#test the existence of an element in the textarea
            {
                if ($_POST["list_query"] == "PMID" OR $_POST["list_query"] == "ELocationID")#condition according to the choice of the PMID and DOI drop-down list
                {
                    $pmid = trim($_POST["textarea"]);
                    $listpmid = explode("\n", str_replace("\r\n", "\n", $pmid));//creation of the PMID or DOI list for the query
                    $listpmid = array_values(array_unique($listpmid));//checking that there are no duplicates
                       
                }
                elseif ($_POST["list_query"] == "Author")#condition according to the choice in the drop-down list Author
                {
                    $nb = strval($_POST['retmax']); //number of items to be recovered
                    $base = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&retmax=' . $nb .'&usehistory=y&term=';//variable containing the query to retrieve the list of PMIDs according to the query criteria
                    $text = urlencode(rtrim(strip_tags($_POST["textarea"])));
                    $search_pmid = file_get_contents($base . $text . "[" . $_POST['list_query'] . "]");//launch of the request
                    $search_pmid = new SimpleXMLElement($search_pmid);//creation of the XML parsing object
                    foreach ($search_pmid->IdList->Id as $id)
                    {
                        $listpmid[] = $id;
                    }
                }
                elseif ($_POST["list_query"] == "Title")//condition according to the choice in the drop-down list Title
                {
                    $base = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&usehistory=y&term=';//ariable containing the query to retrieve the list of PMIDs according to the query criteri
                    $text = urlencode(rtrim(strip_tags($_POST["textarea"])));
                    $search_pmid = file_get_contents($base . $text . "[" . $_POST['list_query'] . "]");//launch of the request
                    $search_pmid = new SimpleXMLElement($search_pmid);//creation of the XML parsing object
                    $listpmid[] = $search_pmid->IdList->Id;
                }
                elseif ($_POST['list_query'] == 'dp')//special case for the publication date
                {
                    $nb = strval($_POST['retmax']); 
                    $base = 'https://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&retmax=' . $nb .'&usehistory=y&term=';
                    $text = strval(rtrim(strip_tags($_POST["textarea"])));
                    $date = "[" . strval(strip_tags($_POST['list_query'])) . "]";
                    $year_req = '"' . $text . '"' . $date . ':' . '"' . $text . '"' . $date;
                    $search_pmid = file_get_contents($base . $year_req);//launch of the request
                    $search_pmid1 = new SimpleXMLElement($search_pmid);//creation of the XML parsing object
                    foreach ($search_pmid1->IdList->Id as $id)
                    {
                        $listpmid[] = $id;
                    }

                }
            }
            elseif (isset($_FILES["myfile"]) AND $_FILES["myfile"]["error"] == 0)//management in case of file transfer with pmid
            {
                $size = $_FILES["myfile"]["size"];
                $name = $_FILES["myfile"]["tmp_name"];
                $file = fopen($name, "r");
                $lines = rtrim(fread($file, $size));
                $listpmid = explode("\n", $lines);
            }
            //At the end of this condition, whatever the choice, we return a list of pmid that can be equal to a single pmid
            if (!empty($listpmid))
            {
                //Creating the table to be displayed on the article search results page
                echo "<h1 class='pb-4'>Results</h1>";
                $global_check = "<input class='form-check-input check' id='global_check' type='checkbox' onchange = 'checked_check(this)' name = 'global_check' onclick = 'check(this)'>";
                echo "<table class='table table-responsive table-hover table-bordered'><thead>\n<tr class='table-info'><th>NUM ACCESS</th><th class = 'sort_column'>Title</th><th class = 'sort_column'>Authors</th><th>" . $global_check . "</th></tr>\n</thead><tbody>";
                $i = 0;
                
                while($i < count($listpmid))//boucle sur la liste de pmid remplissant les conditions
                {   
                    $id = $listpmid[$i];
                    $output = search($listpmid, $i);//function in request.php that retrieves the formal xml of each article 
                    $list_info = recovery($output);//function in request.php that parses the xml and returns in a list the important information to store
                    if (!empty($list_info))
                    {

                        $origin = "";
                        $num_access = $list_info[0];
                        $doi = $list_info[1];
                        $pmcid = strval($list_info[2]);
                        $title = $list_info[3];
                        $title = str_replace('"', "'", $title);
                        $year = $list_info[4];
                        $abstract = $list_info[5];
                        $abstract = str_replace('"', "'", $abstract);
                        $authors = $list_info[6];
                        $journal = $list_info[7];
                        $listauthors = $list_info[8];
                        $listauthor = implode(', ', $listauthors);
                        if(preg_match('/[0-9]/', $num_access))
                        {
                            $origin = 'pubmed';
                        }
                        else
                        {
                            $origin = 'doi';
                        }
                        //instantiation of the article object to transmit the information for insertion in the database
                        $object_article = new Article($origin, $num_access, $title, $abstract, $year, $journal, $pmcid, '1', $listauthor, $_SESSION['userID']);
                        $list_objects[$num_access] = $object_article;//creation of the object list according to the accession number
                        $check = "<input type='checkbox' onchange = 'checked_check(this)' class='form-check-input check' name='check[]' id = $num_access value= '" . $object_article->getnum_access($num_access) . "'>\n";
                        $survol = '<a class="survol_title" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="auto" data-bs-content= "' . $abstract . "\">" ;
                        $survolauthor = '<a class="survol_authors" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="auto" data-bs-content= "<strong>Auteurs</strong>' . $listauthor . "<br><strong>Journal:</strong> " . $journal . "<br><strong>Year:</strong> " . $year . '">';
                        //Displaying the rows of the results table.
                        echo "<tr><td>" .  $num_access . "</td>\n<td>" . $survol . trim($title) . "</a></td>\n<td>" . $survolauthor . $authors[0] . ", ... , " . end($authors) . "</a></td>\n<td>" . $check . "</td></tr>\n" ;
                    }
                    else
                    {
                        echo " <div class='alert alert-danger' role='alert'>No articles could be retrieved from Pubmed</div>";
                    }
                    $i++;
                }
                echo "</tbody></table>";
                echo "<p><input class='btn btn-outline-success' id='insert' type='submit' value='Insert'></p>";

            }
            else
            {
                header("Location:./form.php");
                echo "<p>Merci de remplir un champ demandés </p>";
                
            }
            $_SESSION["list_articles"] = $list_objects;//passing our associative array to a session variable for transmission to the next page

        ?>
    </form>
</div>
        <script>
            var listNumAccessDb = <?php echo json_encode($list_num_access_already_present); ?>;//transfer the list to JavaScript to block the insertion of those already present in one of the two databases 
        </script>
<?php
         
    include('../views/footer.php');
?>
