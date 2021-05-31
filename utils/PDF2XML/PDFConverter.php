<?php
    /**
     * 
     * PDFConverter
     * 
     * Created on Wed May 12 2021
     * Latest update on Wed May 12 2021
     * Info - PHP script to , from a pdf link, returns a xml of its content.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     */

    //Prepare request
    if(!isset($_GET['PATH']) && !isset($_GET['URL']) && !isset($_GET['type'])) {
        echo '<div class="alert alert-danger" role="alert">
            This page need an argument: ?PATH=folder&URL=URL&type=[HTML/XML/TEXT]<br>(&print to print the article).
        </div>';
        exit(10);
    }

    $url = urldecode($_GET['URL']);
    $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $url);
    $file = mb_ereg_replace("([\.]{2,})", '', $file);
    $path = $_GET['PATH'].$file;

    file_put_contents($path, fopen($url, 'r'));
    
    //$data = shell_exec("python convert.py ".$path." ".$_GET['type']);
    if(file_exists("convert.py")) {

    }
?>