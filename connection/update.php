<?php
//CLASS IMPORT
include('../views/header.php');

/*
 * Created on Mon May 17 2021
 * Latest update on Tue May 18 2021
 * Info - PHP for members or self udpate management
 * Status Code Returned:
 * - 401 Unauthorized, if we are not expert (case of update member)
 * - 403 Forbidden, if the new name to update already exist (case of update member and update user) 
 * - 405 Method Not Allowed, if the password missmatch (case of update user)
 * - 500 Internal Server Error, if we had a database error
 * - 200 OK, if success
 * - 400 Bad Request, if the parameters aren't good
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
?>
<?php
    //Case of update member
    if(isset($_GET['username']) && isset($_GET['email']) && isset($_GET['password']) && isset($_GET['id'])) {
        $cols = array();
        array_push($cols, array("name_user", $_GET['username']), array("email", $_GET['email']), array("profile", $_GET['profile']));
        if(!empty($_GET['password'])) {
            array_push($cols, array("password", password_hash($_GET['password'], PASSWORD_DEFAULT)));
        }
        if(!empty($_GET['profile'])) {
            array_push($cols, array("password", password_hash($_GET['password'], PASSWORD_DEFAULT)));
        }
        $res = $manager->getSpecific(array("id_user"), array(array("id_user", $_GET['id'])), "user");
        $check = $manager->getSpecific(array("profile"), array(array("name_user", $_SESSION['username'])), "user")[0]['profile'];
        if($check != 'expert') { http_response_code(401); }
        else if(!$res) { http_response_code(403); }
        else {
            $cond = array(array("id_user", $_GET['id']));
            $res = $manager->updateSpecific($cols, $cond, "user");
            ($res) ? http_response_code(200) : http_response_code(500);
        }
    } 
    //Case of update user
    else if(isset($_GET['username']) && isset($_GET['email']) && isset($_GET['password']) && isset($_GET['currentPassword'])) {
        $cols = array();
        array_push($cols, array("name_user", $_GET['username']), array("email", $_GET['email']));
        if($_GET['username'] != $_SESSION['username']) {
            $res = $manager->getSpecific(array("name_user"), array(array("name_user", $_GET['username'])), "user");
            if(!empty($res)) { http_response_code(403); echo http_response_code(); exit(1); }
        }
        if(!empty($_GET['password']) && !empty($_GET['currentPassword'])) {
            $res = $manager->getSpecific(array("password"), array(array("id_user", $_SESSION['userID'])), "user");
            if(!$res || !password_verify($_GET['currentPassword'], $res[0]["password"])) { http_response_code(405); echo http_response_code(); exit(1); } 
            else {
                array_push($cols, array("password", password_hash($_GET['password'], PASSWORD_DEFAULT)));
            }
        }
        $cond = array(array("id_user", $_SESSION['userID']));
        $res = $manager->updateSpecific($cols, $cond, "user");
        if($res) {
            http_response_code(200);
            $_SESSION['username'] = $_GET['username'];
            $_SESSION['userName'] = $_GET['username'];
            if(isset($_COOKIE['cookie-session'])) {
                $userConnection->generateCookie(array($_SESSION['userID'], $_SESSION['username']));
            }
        } else { http_response_code(500); }
    } else {
        http_response_code(400);
    }
    echo http_response_code();
?>