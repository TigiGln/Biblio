<?php
//CLASS IMPORT
require('../POO/class_main_menu.php');
/*
 * Created on Mon May 17 2021
 * Latest update on Tue May 18 2021
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @author Thierry Galliano
 */
include('../views/header.php');
//Menu
(new mainMenu('Members_Management', $manager))->write();
?>

<div class="flex p-4 w-100 overflow-auto" style="height: 100vh;">
<h1>Members Management</h1> 
<div class="pt-4" id="info"><!----></div>
<div class="row justify-content-start">
    <div class="col-md-auto">
            <button id="userForm" type="button" class="formButton btn btn-outline-info" onclick="showUserForm()">Your Information</button>
    </div>
    <?php 
    if($manager->getSpecific(array("profile"), array(array("id_user", $_SESSION['userID'])), "user")[0]['profile'] == "expert") {
        echo '<div class="col-md-auto">
                <button id="addForm" type="button" class="formButton btn btn-outline-info" onclick="showAddForm()">Add Member</button>
            </div>
            <div class="col-md-auto">
                <button id="manageForm" type="button" class="formButton btn btn-outline-info" onclick="showManageForm()">Manage Members</button>
            </div>';
    }
?>
</div>
<div id="form" class="pt-4 w-100"><!----></div>
</div>
<script src="management.js"></script> 
<script src="../utils/buttonsFormsGestion.js"></script> 
<?php
include('../views/footer.php');
?>