<?php
//IMPORT CLASSES
require("../POO/class_saveload_strategies.php");

/**
 * ArticleFetcher
 * 
 * Created on Fri Apr 30 2021
 * Latest update on Tue May 18 2021
 * Info - PHP Class to fetch the xml content of the articles.
 * Usage: refers to the readArticle.php file: Do the followings
 * Instantiate object, call doExist(NUMACCESS), is true call hasRights(), if true call fetch(), fetch() will return true if could fetch, false else with an error message.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
class ArticleFetcher {

    protected $origin;
    protected $numaccess;
    protected $article;
    protected $saveload;
    
    /**
     * __construct
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $numaccess
     *            the NUM_ACCESS of the article to whom we will fetch the xml content in the database or download it.
     * @return void
     */
    public function __construct($origin, $numaccess, $manager) {
        $this->origin = $origin;
        $this->numaccess = $numaccess;
        $this->saveload = new SaveLoadStrategies("../", $manager);
    }
    
    /**
     * doExist function will check if an article with this num_access do exist in the database.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true if an article of num_access exist, false if not (with an error message).
     */
    public function doExist() {
        $cols = array(); array_push($cols, "origin", "num_access");
        $conditions = array(); array_push($conditions, array("num_access", $this->numaccess),  array("origin", $this->origin));
        if($this->saveload->checkAsDB("article", $cols, $conditions)) { 
            $this->article = $this->saveload->loadAsDB("article", array("*"), $conditions, null)[0];
            return true;
        } else {
            $errorCode = 404;
            $this->printError("danger", 'Couldn\'t find article with NUMACCESS='.$this->numaccess.' from '.$this->origin.' in the database.', $errorCode);
            http_response_code($errorCode); 
            return false;
        }
    }

    /**
     * getter on the article item
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return the stored article item
     */
    public function getArticle() {
        return $this->article;
    }
    
    /**
     * hasRights function will check if an userID have the rights to work on an article, given previously with doExist().
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $userID
     * @return true if the given userID do have the right to work on this article of num_access, false if not (with an error message).
     */
    public function hasRights($userID) {
        if(($this->article['user'] == $userID) || $this->article['status'] == 3 || $this->article['status'] == 4) {
            return true;
        } else {
            $errorCode = 403;
            $this->printError("danger", 'You don\'t have the right to work on this article. If you think you had the rights, please refer this issue to your administrator or your team.', $errorCode);
            http_response_code($errorCode); 
            return false;
        }
    }

    /**
     * fetch function will fetch the article depending of if it have a PMCID or not. If it does, will return fetchByPMCID to get the xml of the article and the success boolean.
     * For now no other version are available, hence if the article don't have a pmcid, will return an error.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true if the fetch was sucessful, false if not.
     */
    public function fetch() {
        //todo fetch if a pmcid was added
        switch($this->article['origin']) {
            case "pubmed":
                if(!empty($this->article['pmcid'])) { return $this->fetchByPMCID(); }
                return true;
                break;
            case "default":
                $errorCode = 400;
                $this->printError("warning", 'Couldn\'t fetch article with NUMACCESS='.$this->numaccess.' from '.$this->origin.'. It is either because an error occured, either because we can\'t yet download this kind of article 
                (it depends of the database and/or the journal of this publication). Please refer this issue to your administrator or your team.', $errorCode);
                http_response_code($errorCode); 
                return false; 
        }
    }

     /**
     * fetchByPMCID function will fetch the article in two ways: if the xml isn't stored yet, call the fetchPMC() function to get it. 
     * Then if the xml isn't empty (or its an error), echo the xml content.
     * For now no other version are available, hence if the article don't have a pmcid, will return an error.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true if the fetch of xml content was sucessfull, false if not or empty
     */
    public function fetchByPMCID() {
        if(empty($this->article['html_xml'])) { $this->fetchPMC(); }
        if(!empty($this->article['html_xml'])) {
            //echo $this->article['html_xml'];
            return true;
        } else {
            $errorCode = 400;
            $this->printError("warning", "Couldn't fetch article with NUMACCESS=".$this->numaccess.' from '.$this->origin.". Please refer this issue to your administrator or your team.", $errorCode);
            http_response_code($errorCode); 
            return false; 
        }
        
    }

     /**
     * fetchPMCID function will download the content of the PMC page using the fromPMCID.php script and store it in database (called with addHTMLXMLByPMCID).
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true if the fetch and download of xml content was sucessfull, false if not or if an error occured
     */
    public function fetchPMC() {
        $this->saveload->DB()->addHTMLXMLByPMCID($this->article['num_access'], $this->article['pmcid']);
        $this->article = $this->saveload->loadAsDB("article", array("*"), array(array("num_access", $this->article['num_access'])), null)[0];
        if(!empty($this->article['html_xml'])) { return true; }
        else { return false; }
    }

    /**
     * fetchHTML will, depending of the article characteristic, be able to link the corresponding pdf.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true the fetch of html content was sucessfull, false if not or if an error occured
     */
    public function fetchHTML($tags) {
        if($this->article['origin'] == "pubmed") {
            if(!empty($this->article['pmcid']) && !empty($this->getArticle()['html_xml'])) {                
                return '<div id="html" class="switchDisplay"'.$tags.'>'.$this->getArticle()['html_xml'].'</div>';
            }
        }
        return false;
    }

     /**
     * fetchPDF will, depending of the article characteristic, be able to link the corresponding pdf.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true the fetch of pdf content was sucessfull, false if not or if an error occured
     */
    public function fetchPDF($tags) {
        if($this->article['origin'] == "pubmed") {
            if(!empty($this->article['pmcid'])) {               
                return '<div id="pdf" class="switchDisplay"'.$tags.'><iframe class="w-100" style="height: 90vh;" src="'.'https://www.ncbi.nlm.nih.gov/pmc/articles/'.$this->article['pmcid'].'/pdf/'.'"></iframe></div>';
            }
            else {
                $doi = false;
                $search = 'http://www.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=Pubmed&usehistory=y&term='.$this->article['num_access'];
                $search = file_get_contents($search);
                $search = new SimpleXMLElement($search);
                $web_env = $search->WebEnv;
                $search = "http://www.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?rettype=abstract&retmode=xml&db=Pubmed&query_key=1&WebEnv=".$web_env;
                $search = file_get_contents($search);
                $search = new SimpleXMLElement($search);
                $IdList = $search->PubmedArticle->PubmedData->ArticleIdList;
                foreach ($IdList->ArticleId as $articleId) {
                    if($articleId->attributes()['IdType'] == "doi") {
                        $doi = strval($articleId);
                    }
                }
                if($doi) {
                    /* use an xml file to save links, to avoid to fetch each time */
                    $doiString = "doi".str_replace("/", "_", $doi);
                    $doiString = str_replace("(", "_", $doiString);
                    $doiString = str_replace(")", "_", $doiString);
                    $doi2link = $this->saveload->loadAsXML("../utils/doi2link.xml", "DOI", $doiString, null);
                    if($doi2link != '["empty"]') { 
                        $link = substr($doi2link, 1, -1);
                        $link = json_decode($doi2link, true)[1]['link'];
                    }
                    else {
                        // if wasn't found, go fetch it */
                        include("../utils/fromDOI/fromDOI.php");
                        $xml_data = DOI_CrossRef($doi);
                        
                        if(isset($xml_data->message->link->item0->URL[0])) {
                            $link = DOI_parse($doi, $xml_data->message->link->item0->URL[0], "PDF");
                            //Catch if an error message was returned from DOI parser, if so print it
                            if(strpos('/'.$link, "[ERROR]")) { 
                                echo '<div class="alert alert-danger" role="alert">'.$link.'
                                <form class="form-group" method="post" action="../utils/addPDF.php" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <input class="btn btn-danger" type="submit" value="Manually Add File" id="submit">
                                    </div>
                                    <div class="col">
                                        <input class="form-control btn-danger bg-danger" type="file" name="file" id="file" accept="application/pdf" required>
                                        <input type="hidden" name="doiString" value="'.$doiString.'" />
                                        <input type="hidden" name="doi" value="'.$doi.'" />
                                    </div>
                                </div>
                                </form>
                                </div>';
                                return false; 
                            } else {
                                $datas = array("DOI", array(array($doiString, "value", $doi), array(array("link", $link))));
                                $this->saveload->saveAsXML("../utils/doi2link.xml", $datas, true);
                            }
                        }
                    }
                    if(isset($link) && !empty($link)) {
                        return '<div id="pdf" class="switchDisplay"'.$tags.'>
                            <iframe class="w-100" style="height: 90vh;"src="'.$link.'"></iframe>
                            <form class="form-group" method="post" action="../utils/addPDF.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-auto">
                                    <input class="btn btn-light" type="submit" value="Manually Add File" id="submit">
                                </div>
                                <div class="col">
                                    <input class="form-control btn-light bg-light" type="file" name="file" id="file" accept="application/pdf" required>
                                    <input type="hidden" name="doiString" value="'.$doiString.'" />
                                    <input type="hidden" name="doi" value="'.$doi.'" />
                                </div>
                            </div>
                            </form>
                        </div>';
                    } 
                } 
            }
        }
        return false;
    }

    /**
     * fetchXML will, depending of the article characteristic, be able to link the corresponding xml.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return true the fetch of xml content was sucessfull, false if not or if an error occured
     */
    public function fetchXML($tags) {
        if($this->article['origin'] == "pubmed") {
            if(!empty($this->article['pmcid'])) {
                $_GET['PMCID'] = $this->article['pmcid'];
                $_GET['xml'] = "";
                $xml = include('../utils/fromPMCID/fromPMCID.php');    
                return '<div id="xml" class="switchDisplay w-100" '.$tags.'>'.$xml.'</div>';
            }
        }
        return false;
    }

    public function printError($type, $content, $errorCode) {
        echo '<div class="alert alert-'.$type.'" role="alert">'.$content.'<br>[ERROR CODE: '.$errorCode.']</div>';
    }
}
?>