<?php
//Creation of a drop-down menu with the selected items in the right place and the desired options with conditions according to the pages.
function gestion_select($name, $value_a_tester, $id, $list_value_listbox, $tag_status, $user)
{
    $listbox = '';
    //Disable change in drop-down menus for articles of other members with undefined or tasks status
    if($tag_status == 'members_tasks' || $tag_status == 'members_undefined')
    {
        $listbox .= "<select class='form-select' name= '$name' id = '$id' onchange='changeSelect(this)' disabled>";
    }
    else if ($tag_status == 'processed' || $tag_status == 'rejected')//Disabling change in drop-down menus for other members' items so status when status is proccessed or rejected
    {
        if(strpos($name, 'status_') === 0 AND $user != $_SESSION['username'])
        {
            $listbox .= "<select class='form-select' name= '$name' id = '$id' onchange='changeSelect(this)' disabled>";
        }
        else
        {
            $listbox .= "<select class='form-select' name= '$name' id = '$id' onchange='changeSelect(this)'>";
            
        }
    }
    else
    {
        $listbox .= "<select class='form-select' name= '$name' id = '$id' onchange='changeSelect(this)'>";
    }
    if($value_a_tester == 'undefined')//Remove the processed status in the undefined table
    {
        $index = array_search('processed', $list_value_listbox);
        array_splice($list_value_listbox, $index, 1);
    }
    else if($value_a_tester == 'tasks')//Remove the undefined status in the tasks table
    {
        $index = array_search('undefined', $list_value_listbox);
        array_splice($list_value_listbox, $index, 1);
        //$list_value_listbox = array_diff($list_value_listbox, ['undefined', 'processed']);
    }
    else if($value_a_tester == 'rejected')//Remove the processed status in the rejected table
    {
        $index = array_search('processed', $list_value_listbox);
        array_splice($list_value_listbox, $index, 1);
    }
    else if ($value_a_tester == 'processed')//Remove the rejected status in the processed table
    {
        $index = array_search('rejected', $list_value_listbox);
        array_splice($list_value_listbox, $index, 1);
    }
    foreach($list_value_listbox as $value)//Creating drop-down menu options
    {
        if (strtolower($value) == strtolower($value_a_tester))
        {
            $listbox .= "<option value=$value selected>" . ucfirst($value) . "</option>";
        }
        else
        {
            $listbox .= "<option value=$value>" . ucfirst($value) . "</option>";
        }
    }
    $listbox .= " </select>";
    return $listbox;
}

//function to display the table according to the desired page.
function search_table_status($status, $user, $manager)
{
    
    $enum_status = $manager->search_enum_fields('status', 'article', 'name_status', 'status');
    $enum_user = $manager->search_enum_fields('user', 'article', 'name_user', 'user');
    $table_a_afficher = "";
    $tag_status = $status;
    if($status == 'undefined' OR $status =='tasks')
    {
        #query on the article table of the database according to the status and the user
        $table_a_afficher = $manager->get_fields('article', 'status', 'user', 'name_status', $status, 'name_user', '=' ,$user);
    }
    else if($status == 'members_tasks' OR $status == 'members_undefined')
    {
        //query on the article table of the database according to the status and different from the user
        $tag_status = $status;
        $status = explode('_', $status);
        $status = $status[1];
        $table_a_afficher = $manager->get_fields('article', 'status', 'user', 'name_status', $status, 'name_user', '!=' ,$user);
    }
    else
    {
        //query on the article table of the database according to the status only
        $table_a_afficher = $manager->get_fields_join('article', 'status', 'user', 'name_status', $status);
    }
    
    #creation of the table to be displayed line by line according to the requested status and the user
    echo "<h1>" . str_replace("_", " ", $tag_status) . " articles</h1>";
    if ($tag_status == 'tasks')//Displaying the PMCID and Note column in the case of a tasks status
    {
        echo "<table class='table table-responsive table-hover table-bordered'><thead><tr class='table-info'><th class='sortable' width=12.5%>PMID</th><th>PMCID</th><th class = 'sort_column'>Title</th><th width=20% class = 'sort_column'>Authors</th><th width=12.5%>Status</th><th width=12.5%>User</th><th width=12.5%>Notes</th></tr></thead><tbody>";
    }
    else if ($tag_status == 'rejected')
    {
        echo "<table class='table table-responsive table-hover table-bordered'><thead><tr class='table-info'><th class='sortable' width=12.5%>PMID</th><th class = 'sort_column'>Title</th><th width=20% class = 'sort_column'>Authors</th><th width=12.5%>Status</th><th width=12.5%>User</th><th>Delete</th></tr></thead><tbody>";
    }
    else
    {
        echo "<table class='table table-responsive table-hover table-bordered'><thead><tr class='table-info'><th width=12.5%>PMID</th><th width=30% class = 'sort_column'>Title</th><th width=20% class = 'sort_column'>Authors</th><th width=12.5%>Status</th><th width=12.5%>User</th></tr></thead><tbody>";
    }
    foreach($table_a_afficher as $line_table)//Retrieval of items to be displayed line by line
    {
        $list_authors = $manager->retrieve_authors_article($line_table["num_access"]);
        $listauthor = implode(', ', $list_authors);
        $origin = $line_table["origin"];
        $num_access = $line_table["num_access"];
        $pmcid = $line_table['pmcid'];
        $journal = $line_table['journal'];
        $year = $line_table['year'];
        $name_id_status = 'status_' . $num_access;
        $name_id_user = 'user_'. $num_access;
        $user_name = $line_table['name_user'];
        $list_status = gestion_select($name_id_status, $status, $num_access, $enum_status, $tag_status, $user_name);//creation of the status drop-down menu
        $list_user = gestion_select($name_id_user, $user_name, $num_access, $enum_user, $tag_status, $user_name);//creation of the user drop-down menu
        $abstract =  str_replace('"', "'", $line_table['abstract']);//handling of inverted commas in abstracts
        $title = str_replace('"', "'", $line_table['title']);//handling of inverted commas in titles
        $lien_pubmed  = "<a href ='https://pubmed.ncbi.nlm.nih.gov/$num_access/' target='_blank'>";//addition of a link to the article's pubmed page
        $toolLink = '';
        //management of the redirection when clicking on the title according to the available elements
        if ($tag_status == 'tasks' || $tag_status == 'rejected' || $tag_status == 'processed')
        {
            if($line_table["pmcid"] != "")
            {
                
                $toolLink = 'href="../tools/readArticle.php?NUMACCESS=' . $num_access . '&ORIGIN=' . $origin . '" target="_BLANK"';
            }
            else
            {
                $toolLink = "href='../modules/edit_article_menu/cazy/cazy_table.php?body=1&NUMACCESS=" . $num_access . "' target='_BLANK'"; 
            }
        }
        //creation of title and author overviews
        $survol_title = '<a ' . $toolLink . ' style = "color: #000; font-weight: bold;" class="note" data-bs-toggle="popover" data-bs-placement="auto" data-bs-trigger="hover" data-bs-content="' . $abstract . '">';
        $survolauthor = '<a style="color:#000; font-weight:bold; text-decoration:none;" class="survol_authors" data-bs-toggle="popover" data-container="body" data-bs-trigger="hover" data-bs-placement="auto" data-bs-html="true" data-bs-content= "<strong>Auteurs</strong> ' . $listauthor . "<br><strong>Journal:</strong> " . $journal . "<br><strong>Year:</strong> " . $year . '">';
        //management of general notes.
        if ($tag_status == 'tasks')
        {   
            if(!class_exists("SaveLoadStrategies")) 
            { 
                require('../POO/class_saveload_strategies.php'); 
            }
            $load = new SaveLoadStrategies("..", $manager);
            $notes = '';
            $res = json_decode($load->loadAsXML("../modules/edit_article_menu/notes/notes.xml", $line_table["origin"] . "_" . $line_table["num_access"], "author", $_SESSION['username']), true);
            if (isset($res[0]))
            {  
                $notes = urldecode($res[0]['content']);
                $notes = trim($notes);
                $general_note = strip_tags($notes, '<div>');
                $id_article = $manager->db->query("SELECT id_article FROM article WHERE num_access = $num_access");
                $id_article = $id_article->fetch();
                $id_article = $id_article['id_article'];
                $id_user = $manager->db->query("SELECT id_user FROM user WHERE name_user = '$user'");
                $id_user = $id_user->fetch();
                $id_user = $id_user['id_user'];
                if($manager->get_exist_multiple('general_note', 'id_article', $id_article, 'id_user', $id_user))
                {
                    $update_general_note = $manager->db->prepare("UPDATE general_note SET general_note = :general_note WHERE id_article = $id_article");
                    $update_general_note->bindValue(':general_note', $general_note);
                    $update_general_note->execute();
                }
                else
                {
                    $insert_general_note = $manager->db->prepare("INSERT INTO general_note(id_article, id_user, general_note, date_note) VALUES(:id_article, :id_user, :general_note, NOW())");
                    $insert_general_note->bindValue(':id_article', $id_article);
                    $insert_general_note->bindValue(':id_user', $id_user);
                    $insert_general_note->bindValue(':general_note', $general_note);
                    $insert_general_note->execute();
                }
            }
            else
            {
                $notes = "No note available for the moment";
            }
            echo "<tr id = 'line_$num_access'><td width=12.5%>" . $lien_pubmed .  $num_access . "</a></td><td>" . $pmcid ."</td><td width=30%>" . $survol_title . $title . "</a></td><td width= 20%>" . $survolauthor . $list_authors[1] . ", ... , " . end($list_authors) . "</a></td><td width=12.5%>" . $list_status . "</td><td width=12.5%>" . $list_user . "</td><td width=12.5%>" . $notes . "</td></tr>" ;
        }
        else if($tag_status == 'rejected')
        {
            $delete = "<input type='button' onclick='delete_article($num_access)' value='Del' id='delete_" . $num_access . "'>";
            echo "<tr id = 'line_$num_access'><td width=12.5%>" . $lien_pubmed .  $num_access . "</a></td><td width=30%>" . $survol_title . $title . "</a></td><td width= 20%>" . $survolauthor . $list_authors[1] . ", ... , " . end($list_authors) . "</a></td><td width=12.5%>" . $list_status . "</td><td width=12.5%>" . $list_user . "</td><td>" . $delete . "</td></tr>" ; 
        }
        else
        {
            echo "<tr id = 'line_$num_access'><td width=12.5%>" . $lien_pubmed .  $num_access . "</a></td><td width=30%>" . $survol_title . $title . "</a></td><td width= 20%>" . $survolauthor . $list_authors[1] . ", ... , " . end($list_authors) . "</a></td><td width=12.5%>" . $list_status . "</td><td width=12.5%>" . $list_user . "</td></tr>" ; 
        }     
    }
    echo "</tbody></table>";
    //display the refresh PMCID button if the status is tasks
    if($tag_status == 'tasks')
    {
        echo "<input type='submit' id='refresh' value='Refresh PMCID'>";
    }
}



?>