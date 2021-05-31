<?php
/**
 * pdfDownloader
 * 
 * Created on Mon May 17 2021
 * Latest update on Mon May 17 2021
 * Info - PHP Class to download pdf ressources given a link in get, use of a path
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
if(!isset($_GET['PATH']) && !isset($_GET['URL'])) {
    http_response_code(400);
} else {
    $url = urldecode($_GET['URL']);
    $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $url);
    $file = mb_ereg_replace("([\.]{2,})", '', $file);
    $path = $_GET['PATH'].$file;
    file_put_contents($path, fopen($url, 'r'));
    http_response_code(200);
}
?>

