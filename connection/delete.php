<?php
//CLASS IMPORT
include('../views/header.php');

/*
 * Created on Mon May 17 2021
 * Latest update on Fri May 21 2021
 * Info - PHP for members delete management
 * Status Code Returned:
 * - 401 Unauthorized, if we are not expert
 * - 403 Forbidden, if the member to delete don't exist
 * - 500 Internal Server Error, if we had a database error
 * - 200 OK, if success
 * - 400 Bad Request, if the parameters aren't good
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
?>
<?php
    if(isset($_GET['username']) && isset($_GET['id'])) {
        $cols = array();
        array_push($cols, array("name_user", $_GET['username']), array("id_user", $_GET['id']));
        $res = $manager->getSpecific(array("id_user"), array(array("id_user", $_GET['id'])), "user");
        $check = $manager->getSpecific(array("profile"), array(array("name_user", $_SESSION['username'])), "user")[0]['profile'];
        if($check != 'expert') { http_response_code(401); }
        else if(!$res) http_response_code(403);
        else {
            //give articles to actual user
            $res = $manager->updateSpecific(array(array("user", $_SESSION['userID'])), array(array("user", $_GET['id'])),"article");
            if(!$res) { http_response_code(500); }
            else {
                //if could give, then delete user
                $res = $manager->deleteSpecific(array(array("id_user", $_GET['id'])), "note");
                $res = $manager->deleteSpecific($cols, "user");
                ($res) ? http_response_code(200) : http_response_code(500);
            }
        }
    } else {
        http_response_code(400);
    }
    echo http_response_code();
?>