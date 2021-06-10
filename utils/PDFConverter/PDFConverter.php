<?php
    /**
     * 
     * PDFConverter
     * 
     * Created on Wed May 12 2021
     * Latest update on Tue Jun 01 2021
     * Info - PHP script to , from a pdf link, returns a xml of its content.
     * Some usage based on window:
     * to html
     * "./win/pdfix_app.exe" pdf2html --no-external --format 2 --quality 80 --input "../../../pdf/pdf1-s2.0-S221026122100479X-main (1).pdf" --output "../../../pdf/machin.html"
     * to json
     * "./win/pdfix_app.exe" pages2json --text --input "../../../pdf/pdf1-s2.0-S221026122100479X-main (1).pdf" --output "../../../pdf/machin2.json"
     * to text
     * "./win/pdfix_app.exe" pdf2txt --input "../../../pdf/pdf1-s2.0-S221026122100479X-main (1).pdf" --output "../../../pdf/machin3.json"
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     */
    //Prepare request
    if(!isset($_GET['PATH']) && !isset($_GET['URL']) && !isset($_GET['type']) && !isset($_GET['TOOLPATH'])) {
        echo '<div class="alert alert-danger" role="alert">
            This page need an argument: ?TOOLPATH=folder&PATH=folder&URL=URL&type=[HTML/XML/TEXT]<br>(&print to print the article, &save to save it).
        </div>';
        exit(10);
    }
    $url = urldecode($_GET['URL']);
    $toolPath = urldecode($_GET['TOOLPATH']);
    $file = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $url);
    $file = preg_replace("([\.]{2,})", '', $file);
    $path = $_GET['PATH'].$file;
    file_put_contents(strval($path), fopen($url, 'r'));
    $output = null; $returnValue = null;

    //Usefull parameters
    //general, get an API key here: https://pdfix.net/download-free/#lite-license-key, lite version can work without

    //to convert to html
    $format = 2; //Image format quality (2 is JPG, 1 is PNG)
    $quality = 80; //Quality of the result (0-100)

    //Find the correct usage
    $call = "";
    switch(PHP_OS_FAMILY) { //$action = '"'.$toolPath."pdf2htmlEX.exe\" --zoom 2 --printing 1 --process-outline 0 --optimize-text 1 \"".$path."\" \"".$path.".html\"";
        case "Windows":
            $call = "win/pdfix_app.exe";
            break;
        case "Darwin":
            $call = "mac/pdfix_app";
            break;
        default: //Linux and others
            $call = "linux/pdfix_app";
            break;
    }
    //And the correct command
    $action = "";
    switch($_GET['type']) { 
        case "HTML":
            $action = '"'.$toolPath.$call."\" pdf2html --no-external --no-page-render --preflight --responsive --format ".$format." --quality ".$quality." --input \"../".strval($path)."\" --output \"../".strval($path).".html\"";
            break;
        default: //Linux and others
            $action = "--help";
            break;
    }
    //Action
    echo "action: ".$action;
    exec($action, $output, $returnValue);
    if($returnValue == 0) {
        try {
            $fileData = file_get_contents($path.".html");
            $newData = $fileData;
            //$newData = preg_replace('/(<!DOCTYPE html>)|(<head>)|(<\/head>)|(<meta).*?(\/>)|(#page-container).*?}|(<html).*?(>)|(<div class="loading-indicator">).*?(<\/div>)|(@keyframes fadein{from{opacity:0}).*?(<\/style>)/s', '', $fileData);
            //$newData = preg_replace('/(<\/html>)/s', '<script>document.getElementById("sidebar").style.background = "white"; document.getElementById("page-container").style.background = "white";</script>', $newData);
            if(isset($_GET['print'])) { print_r($newData); }
            if(!isset($_GET['save'])) { 
                unlink($path);
                unlink($path.".html");
             }
            return $newData;
        } catch(Exception $ex) {
            unlink($path);
            return 0;
        }
    }
?>