<?php
    /**
     * 
     * fromDOI
     * 
     * Created on Wed May 12 2021
     * Latest update on Sun May 16 2021
     * Info - PHP script to retrieve and echo using articles from DOI.
     * Us   age debug example http://localhost/test/projet_stage/utils/fromDOI/fromDOI.php?doi=10.1021/bi00696a003&format=PDF&verbose
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     */

     if(isset($_GET['doi']) && isset($_GET['format'])) {
        $xml_data = DOI_CrossRef($_GET['doi']);
        if(isset($xml_data->message->link->item0->URL[0]) && !empty($_GET['format'])) {
            echo DOI_parse($_GET['doi'], $xml_data->message->link->item0->URL[0], $_GET['format']);
        } 
        else { http_response_code(404); }
     } 


    /**
     * DOI_CrossRef
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $doi
     * @return
     */    
    function DOI_CrossRef($doi) {
        //Prepare Curl request
        if(!$doi) return false;
        //return the xml object of the crossref request on the $doi.
        $url = 'https://api.crossref.org/works/'.$doi;
        $req = curl_init($url);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = curl_exec($req);
        //Close Curl request
        curl_close($req); 
        //Parse
        $data = json_decode($res, true);
        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml($data,$xml_data);
        return $xml_data;
    }

    function DOI_parse($doi, $url, $format) {
        switch($url) {
            case (preg_match('/(\.pdf$)|(Pdf\/)|(pdf\/)/', $url)? true : false):
                /* Available links features .pdf directly */
                if(isset($_GET['verbose'])) echo "Article Available directly in pdf from: ".$url."<br>";
                return DOI_parse_pdf($url, $format);
                break;
            case (preg_match('/api.elsevier.com.*?/', $url)? true : false):
                /* Available in Elsevier's Api. Elsevier's Api crossref's links DO NOT features .pdf directly, additionnal parsing is required */
                if(isset($_GET['verbose'])) echo "Article Available in Elsevier API<br>";
                return DOI_parse_elsevierAPI($doi, $format);
                break;
            /*case (preg_match('/pubs.acs.org\/doi\/.*?/', $url)? true : false):
                /* Available in ACS 
                if(isset($_GET['verbose'])) echo "Article Available in ACS Publications<br>";
                //todo overcome block with curl (?)
                return "[WARNING] ACS use of embed local pdf files dissalow us to get the correct path to pdf from their website. PLease add it manually if required: ".$url;
                break;*/
            default:
                if(isset($_GET['verbose'])) echo "Need to implement parsing for: ".$url;
                return "[ERROR] Need to implement parsing for: ".$url;
        }
    }
    /*----------------------------------------------------
     * PARSERS SECTION
    /*----------------------------------------------------*/

    function DOI_parse_elsevierAPI($doi, $format) {
        $url = 'https://api.elsevier.com/content/article/doi/'.$doi;
        $req = curl_init($url);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = curl_exec($req);
        //Close Curl request
        curl_close($req); 
        $xml_data = new SimpleXMLElement($res);
        if(isset($_GET['verbose'])) var_dump($xml_data->coredata->link[1]);
        //link 0 is the api link, link1 is the link of the article. Hence the new link is stored in position 1.
        $url = $xml_data->coredata->link[1]->attributes()->href;
        return DOI_parse_scienceDirect($url, $format);
    }

    function DOI_parse_scienceDirect($url, $format) {
        //Science direct will use pii, not doi, fortunately DOI_parse_elsevierAPI has the pii link already.
        //url given example: https://www.sciencedirect.com/science/article/pii/S0021925821004245
        if(preg_match('/sciencedirect.com.*/', $url)) { 
            //echo "Article Available in Science Direct<br>";
            preg_match('/pii.*/', $url, $prepii); 
            $pii = $prepii[0];
            $pii = str_replace("pii/", "", $pii); $pii = str_replace("/", "", $pii);
            //url to true pdf as: www.sciencedirect.com/science/article/pii/$PII/pdfft?md5=209970e60c64f64e130e9855d385934a&amp;pid=1-s2.0-$PII-main.pdf
            $url = "https://www.sciencedirect.com/science/article/pii/$pii/pdfft?md5=209970e60c64f64e130e9855d385934a&amp;pid=1-s2.0-$pii.pdf";
            return DOI_parse_pdf($url, $format);
        }
    }

    function DOI_parse_pdf($url, $format) {
        switch($format) {
            case "PDF":
                //echo "fetch PDF";
                return $url;
                break;
            case "XML":
                //echo "fetch XML";
                return "";
                break;
            default:
                //echo "Need to implement parsing for: ".$format;
                return false;
        }
    }

    /**
     * array_to_xml
     *
     * @param  mixed $data
     * @param  mixed $xml_data
     * @return void
     */
    function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
         }
    }
?>