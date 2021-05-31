<?php
header('Content-Type: text/plain; charset=utf-8');
//IMPORT CLASSES
require("../POO/class_saveload_strategies.php");
ob_start();
require("../views/header.php");
ob_end_clean();

/**
 * addPDF
 * 
 * Created on Mon May 17 2021
 * Latest update on Mon May 17 2021
 * Info - PHP Class to download pdf ressource given a link.
 * Important - You need to set the value of upload_max_filesize and post_max_size in your php.ini if it wasn't done before:
 * 
 * ; Maximum allowed size for uploaded files.
 * upload_max_filesize = 16M
 * ; Must be greater than or equal to upload_max_filesize
 * post_max_size = 16M
 * 
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
if(!isset($_POST['FILE']) && !isset($_POST['doi']) && !isset($_POST['doiString'])) {
    http_response_code(400);
} else {
    try {
        //Check errors
        if (
            !isset($_FILES['file']['error']) ||
            is_array($_FILES['file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
    
        //Check MIME Type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['file']['tmp_name']),
            array(
                'pdf' => 'application/pdf',
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }
    
        //Save file
        if (!move_uploaded_file($_FILES['file']['tmp_name'], "../pdf/".$_FILES['file']['name'])) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        $saveload = new SaveLoadStrategies("../", $manager);
        //Delete previous file in map if exist
        //Comment this part if you always want to keep previous files
        $doi2link = $saveload->loadAsXML("../utils/doi2link.xml", "DOI", $_POST['doiString'], null);
        if($doi2link != '["empty"]') { 
            $link = substr($doi2link, 1, -1);
            $link = json_decode($doi2link, true)[1]['link'];
            unlink($link);
        }
        //Link in map
        $datas = array("DOI", array(array($_POST['doiString'], "value", $_POST['doi']), array(array("link", "../pdf/".$_FILES['file']['name']))));
        $res = $saveload->saveAsXML("./doi2link.xml", $datas, true);
        if($res == 200) {
            echo "Successfully upload pdf file: ".$_FILES['file']['name']." [".$res."]";
            //Go Back
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        else echo "A save error occured while upload pdf file: ".$_FILES['file']['name']." [".$res."]";
    
    } catch (RuntimeException $e) {
        echo "An error occured when trying to add pdf: ".$e->getMessage();
        exit(10);
    }
}
?>
