<?php
//CLASS IMPORT
ob_start();
include('../views/header.php');
ob_end_clean();
?>
<?php
    $res = $manager->getSpecific(array("name_user", "email"), array(array("name_user", $_SESSION['username'])), "user");
    if(!empty($res)) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    echo json_encode($res);
?>