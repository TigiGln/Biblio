<?php
    
    require "../POO/class_main_menu.php";
    require "../POO/class_connexion.php";
    require "../POO/class_manager_bd.php";
    require "request.php";
?>
<?php
    include('../views/header.php');
    $menu = new MainMenu('Insertion', $manager);
    $menu->write();
?>
<div class="flex p-4 w-100 overflow-auto" style="height: 100vh;">
    <h1>Insertion</h1>
    <div>
        <!-- Creating the article insertion form -->
        <form method="post" action="result.php" enctype="multipart/form-data">
            <!-- Select menu to select our query keyword -->
            <div class="form-group pb-4">
                <select name="list_query" id="list_query" class="form-select w-25" > 
                    <option value="">--Please choose an option--</option>
                    <option value="PMID">PMID</option>
                    <option value="ELocationID">DOI</option>
                    <option value="Author">Author</option>
                    <option value="Title">Title</option>
                    <option value="dp">Year</option>
                </select>
                <!-- number of items needed requested for the Author and Date queries -->
                <input type="number" name="retmax" id='retmax' value="5" min="1" max="100" style='width:50px;'>
            </div>
            <div class="form-group pb-4">
                <!-- keyword(s) input field -->
                <textarea name="textarea" id="textarea" rows="4" class="form-control w-25" ></textarea>
            </div>
            <p>
                <label for="file">My file</label>
                <input type="file" name="myfile" id="myfile" accept=".txt">
            </p>
            <div class="form-group pb-4">
                <input class="btn btn-outline-success" type="submit" value="Start search" id="submit">
            </div> 
            <br>
        </form>
    </div>
    <script src="./gestion_form.js"></script><!-- import of the javascript for the management of the arguments given by the user according to the chosen criteria -->
</div>
<?php
          
    include('../views/footer.php');
?>