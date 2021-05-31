<?php

/**
 * SaveLoadStrategies
 * 
 * Created on Fri Apr 30 2021
 * Latest update on Mon May 17 2021
 * Info - PHP Class for different saves strategies.
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 */
class SaveLoadStrategies 
{

    protected $file;
    protected $position;
    protected $bdInfo;
    protected $connexionbd;
    protected $manager;
    protected $dbSession;
        
    /**
     * __construct
     * if you only gives $path as argument when calling this constructor, will connect to the specified database in the class
     * if you gives $path + 4 arguments for connection, will connect to this database instead
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return void
     */
    public function __construct($position, $manager) 
    {
        $this->position = $position;
        $this->manager = $manager;
        //$this->connect();
    }

    /************************************************************************/
    /*** MANAGER STRATEGIES (DB) ***/
    /************************************************************************/

    
    /**
     * connect will do a connection to the database PDO.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @deprecated
     * @return void
     */
    protected function connect() 
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->connexionbd = new ConnexionDB($this->bdInfo[0], $this->bdInfo[1], $this->bdInfo[2], $this->bdInfo[3], $this->bdInfo[4]);
        $_SESSION[$this->dbSession] = $this->connexionbd;
        $this->manager = new Manager($_SESSION[$this->dbSession]->pdo);
    }

    /**
     * DB will return the database item to directly use its functions for some cases.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return $manager object
     */
    public function DB() 
    {
        return $this->manager;
    }

    /**
     * connexionbd will return the database item to directly use its functions for some cases.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @return $connexionbd object
     */
    public function connexiondb() 
    {
        return $this->connexionbd;
    }
  
    /**
     * checkAsDB allows to check if a field for $conditions exist in the $table of the database.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $table
     * @param  mixed $cols
     * @param  mixed $conditions
     * @return void
     */
    public function checkAsDB($table, $cols, $conditions) 
    {
        $res = $this->manager->getSpecific($cols, $conditions, $table);
        return !empty($res);
    }
    
    /**
     * saveAsDB allows to save (insert or update) data in a $table in the database, given $cols to gives values, $conditions for update.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $table
     *            Table where we perform the request.
     * @param  mixed $cols
     *            Array of arrays to get the conditions SET. in each subArrays position 0 is the left member, position 1 is the right member.
     * @param  mixed $conditions
     *            Array of arrays to get the conditions WHERE. in each subArrays position 0 is the left member, position 1 is the right member.
     * @param  mixed $overwrite
     *            If true, will overwrite (update), if false, will add (insert).
     * @return void
     */
    public function saveAsDB($table, $cols, $conditions, $overwrite) 
    {
        if($overwrite) 
        {
            $res = $this->manager->updateSpecific($cols, $conditions, $table);
            return ($res) ? 200 : 520;
        } 
        else 
        {
            $res = $this->manager->insertSpecific($cols, $table);
            return ($res) ? 200 : 520;
        }
    }
    
    /**
     * loadAsDB allows to load $cols datas in a $table in the database, given $conditions.
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $table
     *            Table where we perform the request.
     * @param  mixed $cols
     *            Array with columns name to get.
     * @param  mixed $conditions
     *            Array of arrays to get the conditions WHERE. in each subArrays position 0 is the left member, position 1 is the right member.
     * @param  mixed $user
     *            Optionnal to homogenize with the saveAsXML. you can write something but it will not be (yet) usefull. Later we can think about strategies to block certains load/get of data depending of the user rank.
     * @return void
     */
    public function loadAsDB($table, $cols, $conditions, $user) 
    {
        $res = $this->manager->getSpecific($cols, $conditions, $table);
        return $res;
    }

    /************************************************************************/
    /*** XML STRATEGIES ***/ 
    /************************************************************************/

    /**
     * saveAsXML
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $file
     *            the path to file to save.
     * @param  mixed $datas
     *            an array of arrays of ... where we save datas.
     *            usage: array(ID, array(array(primarytag, name, value), array(array(tag, value), array(tag, value), ...)))
     *            For now only allow to save one primarytag at a time
     * @param  mixed $overwrite
     *            overwrite if a similar PRIMARY TAG exist
     * @return http response code
     */
    public function saveAsXML($file, $datas, $overwrite) 
    {
        
        $ID = $datas[0];//pubmed_33465378
        $PRIMARYTAGDATA = $datas[1][0][0];//author
        $PRIMARYTAGVALUE = $datas[1][0][1];//name
        $PRIMARYTAGNAME = $datas[1][0][2];//thierry
        $values = $datas[1][1];//array avec date et contenu de la note
        $notes = '';
        if(file_exists($file)) 
        {
            $xml = simplexml_load_file($file);
            if(!isset($xml->$ID)) 
            { 
                $xml->addChild($ID, " "); 
            }
            $didExist = false;
            if($overwrite) 
            {
                foreach ($xml->$ID->{$PRIMARYTAGDATA} as $primaryTag) 
                {
                    $atr = (string) $primaryTag->attributes()[0];
                    if($atr == $PRIMARYTAGNAME) 
                    {
                        $didExist = true;
                        for ($i = 0; $i < sizeof($values); $i++) 
                        {
                            $tag = strval($values[$i][0]);
                            $primaryTag->$tag = strval($values[$i][1]);
                            $notes .= strval($values[$i][1]) . '<br>';
                        }
                    }
                }
            }
            if(!$didExist) 
            {
                $primaryTag = $xml->$ID->addChild($PRIMARYTAGDATA,'');
                $primaryTag->addAttribute($PRIMARYTAGVALUE, $PRIMARYTAGNAME);
                for ($i = 0; $i < sizeof($values); $i++) 
                {
                    $primaryTag->addChild($values[$i][0], strval($values[$i][1]));
                    //$notes .= strval($values[$i][1]) . '<br>';
                }
            }
            echo $notes;
            //chmod($file, 0664);
            $save = $xml->saveXML($file);
            return ($save != false) ?  200 : 424;
        } 
        else 
        { 
            return 404; 
        }
    }

    /**
     * loadAsXML
     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
     * @param  mixed $file
     *            the path to file to save.
     * @param  mixed ID
     *            article ID.
     * @param  mixed PRIMARYTAGDATA
     *            the name of the primary tag, the one right after IDXXX in the xml hierarchy.
     * @param  mixed user
     *            used to separate user data from other, write null or empty string is you dn't want to separate.
     * @return http response code 404 if failure, or content of the requested ID in the xml
     */
    public function loadAsXML($file, $ID, $PRIMARYTAGDATA, $user) 
    {
        if(file_exists($file)) 
        {
            $xml = simplexml_load_file($file);
            if(!isset($xml->$ID)) 
            { 
                return 400; 
            }
            $userArray = ["empty"];
            $othersArray = [];
            foreach ($xml->$ID->{$PRIMARYTAGDATA} as $primaryTag) 
            {
                if($primaryTag->attributes() == $user) 
                {
                    $userArray = [];   
                    array_push($userArray, $primaryTag);
                } 
                else 
                {   
                    array_push($othersArray, $primaryTag); 
                }
            }
            return json_encode(array_merge($userArray, $othersArray));
        } 
        else 
        { 
            return 404; 
        }
    }
}
?>