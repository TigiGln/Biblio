  
<?php
    class Manager
    {
        public $db; // Instance de PDO. 

        public function __construct($db)
        {
            $this->setDb($db);
        }

        public function add(Article $article)
        {
            // Préparation de la requête d'insertion.
            // Assignation des valeurs.
            // Exécution de la requête.
            $requete = $this->db->prepare("INSERT INTO article(origin, num_access, title, abstract, year, journal, pmcid, status, user) VALUES(:origin, :num_access, :title, :abstract, :year, :journal, :pmcid, :status, :user)");
            
            #$requete = $this->db->prepare("INSERT INTO Articles(statut, pmid, doi, pmcid, title, years, abstract, authors, journal) VALUES(:statut, :pmid, :doi, :pmcid, :title, :years, :abstract, :authors, :journal)");
            $requete->bindValue(":origin", $article->getorigin());
            $requete->bindValue(":num_access", $article->getnum_access());
            $requete->bindValue(":title", $article->gettitle());
            $requete->bindValue(":abstract", $article->getabstract());
            $requete->bindValue(":year", $article->getyear());
            $requete->bindValue(":journal", $article->getjournal());
            $requete->bindValue(":pmcid", $article->getpmcid());
            $requete->bindValue(":status", $article->getstatus());
            $requete->bindValue(":status", $article->getstatus());
            $requete->bindValue(":user", $article->getuser());
            #$requete->bindValue(":pmid", strval($article->pmid()));
            #$requete->bindValue(":doi", $article->doi());
            #$requete->bindValue(":authors", $article->authors());

            $res = $requete->execute();
            if (!$requete) 
            {
                echo "\n PDO::errorInfo():\n";
                print_r($db->errorInfo());
            }
            
        }
        public function add_authors(Article $article)
        {
            $listauthors = explode(',', $article->getauthors());
            foreach($listauthors as $name)
            {
                $name = trim($name);
                $identity = explode(' ', $name);
                $request1 = $this->db->prepare("SELECT * FROM authors WHERE name = :name AND firstname = :firstname");
                $request1->bindValue(":name", $identity[0]);
                $request1->bindValue(":firstname", $identity[1]);
                $request1->execute();
                $data = $request1->fetch();
                if(empty($data))
                {
                    $request = $this->db->prepare("INSERT INTO authors(name, firstname) VALUES(:name, :firstname)");
                    $request->bindValue(":name", $identity[0]);
                    $request->bindValue(":firstname", $identity[1]);
                    $request->execute();
                    
                }
            }
        }
        public function add_link_authors_article(Article $article, $num_access)
        {
            $listauthors = explode(',', $article->getauthors());
            $i = 1;
            $request1 = $this->db->prepare("SELECT id_article FROM article WHERE num_access = $num_access");
            $request1->execute();
            $id_article = $request1->fetch();
            foreach($listauthors as $name)
            {
                $name = trim($name);
                $identity = explode(' ', $name);
                $request2 = $this->db->prepare("SELECT id_author FROM authors WHERE name = :name AND firstname = :firstname");
                $request2->bindValue(":name", $identity[0]);
                $request2->bindValue(":firstname", $identity[1]);
                $request2->execute();
                $id_author = $request2->fetch();
                //var_dump($id_author);
                $request3 = $this->db->prepare("INSERT INTO author_article(id_author, id_article, position) VALUES(:id_author, :id_article, :position)");
                $request3->bindValue(":id_author", $id_author['id_author']);
                $request3->bindValue(":id_article", $id_article["id_article"]);
                $request3->bindValue(":position", $i);
                $request3->execute();
                $i++;

            }
            
        }
        public function retrieve_authors_article($num_access)
        {
            $request1 = $this->db->prepare("SELECT id_article FROM article WHERE num_access = $num_access");
            $request1->execute();
            $id_article = $request1->fetch();
            //var_dump($id_article);
            //echo '<br><br>';
            $request2 = $this->db->prepare("SELECT id_author, position FROM author_article WHERE id_article = :id_article");
            $request2->bindValue(":id_article", $id_article["id_article"]);
            $request2->execute();
            $list_id_author = [];
            $list_position = [];
            while($data = $request2->fetch(PDO::FETCH_ASSOC))
            {
                $list_id_author[] = $data['id_author'];
                $list_position[$data['id_author']] = $data['position'];
            }
            //var_dump($list_id_author);
            //echo '<br><br>';
            $list_authors = [];
            foreach($list_id_author as $id_author)
            {
                $request3 = $this->db->prepare("SELECT name, firstname FROM authors WHERE id_author = $id_author");
                $request3->execute();
                $data = $request3->fetch(PDO::FETCH_ASSOC);
                $list_authors[$list_position[$id_author]] = $data['name'] . ' ' . $data['firstname'];
            }
            return $list_authors;
            //echo '<br><br>';
        }

        //insertion pour le formulaire d'enregistrement des utilisateurs
        public function add_form($protocol, $list, $table) 
        {
            $requete = $this->db->prepare("INSERT INTO " . $table ."(" . implode(', ', $list) . ") VALUES(:" . implode(', :', $list) . ")");
            for($i=0 ; $i < count($list) ; $i++)
            {
                if ($list[$i] == 'password')
                {
                    $requete->bindValue(":" . $list[$i], password_hash(htmlspecialchars($protocol[$list[$i]]), PASSWORD_DEFAULT));
                }
                else
                {
                    $requete->bindValue(":" . $list[$i], htmlspecialchars($protocol[$list[$i]]));
                }
            }
            $requete->execute();
            if (!$requete) 
            {
                echo "\nPDO::errorInfo():\n";
                print_r($db->errorInfo());
            }
        }
        public function add_prot_access($table, $id_article, $prot_access)
        {
            //$request = $this->db->prepare("INSERT INTO $table1(id_article, prot_access) VALUES(SELECT id_article, :prot_access FROM $table2 WHERE num_access = :num_access)");
            $request = $this->db->prepare("INSERT INTO $table(id_article, prot_access) VALUES(:id_article, :prot_access)");
            $request->bindValue(":id_article", $id_article);
            $request->bindValue(":prot_access", $prot_access);
            $request->execute();
            if (!$request) 
            {
                echo "PDO::errorInfo():";
                print_r($db->errorInfo());
            }
        }
        //Savoir si une valeur existe dans la table à un champ donnée
        public function get_exist($table, $fields, $value)
        {
            // Exécute une requête de type SELECT avec une clause WHERE.
            $requete = $this->db->prepare("SELECT * FROM  $table  WHERE  $fields = ?");
            //$requete->bindValue(':value', $value);
            $requete->execute(array(htmlspecialchars($value)));
            #$requete = $this->db->query("SELECT pmid, doi, pmcid, title, years, abstract, authors, journal, statut FROM Articles WHERE " .  $key . " = " . $id);
            $donnees = $requete->fetch();
            return !empty($donnees);
            
        }
        public function get_exist_multiple($table, $fields1, $value1, $fields2, $value2)
        {
            $request = $this->db->prepare("SELECT * FROM  $table  WHERE  $fields1 = :value1 AND $fields2 = :value2");
            $request->bindValue(':value1', $value1);
            $request->bindValue(':value2', $value2);
            $request->execute();
            $donnees = $request->fetch();
            return !empty($donnees);
        }
        public function get_num_access($columns, $table)
        {
            $list_elements = [];
            $requete = $this->db->prepare("SELECT $columns FROM $table");
            $requete->execute();
            while($data = $requete->fetch(PDO::FETCH_ASSOC))
            {
                $list_elements[] = $data ["num_access"];
            }
            return $list_elements;
        }
        public function get($fields, $value, $table, $columns = "*")//Récupérer l'élément correspondant à la requête
        {
            $requete = $this->db->prepare("SELECT $columns FROM $table WHERE " . $fields . " = ?");
            $requete->execute(array(htmlspecialchars($value)));
            $donnees = $requete->fetch(PDO::FETCH_ASSOC);

            return $donnees;

        }
        public function get_cazy($base, $columns, $array_tables, $champ_where, $value_where, $champ_join1="", $champ_join2="", $champ_table3="", $champ_where2=1, $value_where2=1)
        {
            if(count($array_tables) == 1 AND count($base) == 1)
            {
                $table1 = $array_tables[0];
                $base1 = $base[0];
                $request = $this->db->prepare("SELECT $columns FROM $base1.$table1 WHERE $champ_where = '$value_where' AND $champ_where2 = $value_where2");
                var_dump($request);
            }
            else if(count($array_tables) == 2 AND count($base) == 2)
            {
                $table1 = $array_tables[0];
                $table2 = $array_tables[1];
                $base1 = $base[0];
                $base2 = $base[1];
                $request = $this->db->prepare("SELECT $columns FROM $base1.$table1 INNER JOIN $base2.$table2 ON $base1.$table1.$champ_join1 = $base2.$table2.$champ_join1 WHERE $champ_where = $value_where AND $champ_where2 = $value_where2");
            }
            else
            {
                $count = 1;
                foreach($array_tables as $tables)
                {
                    ${ 'table' . $count } = $tables;
                    $count++;
                }
                $base1 = $base[0];
                $base2 = $base[1];
                $base3 = $base[2];
                $request = $this->db->prepare("SELECT $columns FROM $base1.$table1 INNER JOIN $base2.$table2 ON $base1.$table1.$champ_join1 = $base2.$table2.$champ_join1 INNER JOIN $base3.$table3 ON $base2.$table2.$champ_join2 = $base3.$table3.$champ_join2 WHERE $champ_where = $value_where AND $champ_where2 = $value_where2"); 
            }
            $request->execute();
            $nb_line_request = $request->rowCount();
            $array_values = [];
            if($nb_line_request > 0)
            {
                while($result = $request->fetch())
                {   
                   $array_values[] = $result[$columns];
                }
                return $array_values;
            }
            
        }
        public function get_fields($table1, $table2, $table3, $champs_status ,$status, $champs_user, $sign ,$user)//Récupération des lignes filtré par la valeur du champs
        {
            $requete = $this->db->prepare("SELECT * FROM $table1 INNER JOIN $table2 ON $table1.status = $table2.id_status INNER JOIN $table3 ON $table1.user = $table3.id_user WHERE $table2.$champs_status = '$status' AND $table3.$champs_user " . $sign . " '$user'");
            $requete->execute();
            $article_list = $requete->fetchAll(PDO::FETCH_ASSOC);

            return $article_list;
            
        }
        public function get_fields_join($table1, $table2, $table3, $champs_status, $status)
        {
            $requete = $this->db->prepare("SELECT *, $table3.name_user FROM $table1 INNER JOIN $table2 ON $table1.status = $table2.id_status INNER JOIN $table3 ON $table1.user = $table3.id_user WHERE $table2.$champs_status = :status");
            $requete->bindValue(':status', $status);
            $requete->execute();
            $article_list = $requete->fetchAll(PDO::FETCH_ASSOC);
            
            return $article_list;
        }
        //fonction de mise à jour des données par le num_access
        public function update($num_access, $fields, $modif, $table1, $table2)
        {
            
            // Prépare une requête de type UPDATE.
            // Assignation des valeurs à la requête.
            // Exécution de la requête.
            $requete = $this->db->prepare("UPDATE $table1 SET $table1.$fields = (SELECT id_$table2 FROM $table2  WHERE $table2.name_$table2 = '$modif') WHERE $table1.num_access = $num_access");
            $requete->bindValue(":status", $modif);
            $requete->execute();
            if($requete)
            {
               echo $fields . ' update successfully completed <br>';
               if ($fields == 'user')
               {
                    echo "The article now belongs to " . $modif . "<br>";
               }
               else
               {
                   echo "The article has the status: " . $modif . "<br>";
               }
                
            }
            else
            {
                echo "a problem occurred when updating the" . $fields;
            }

        }
        //fonction pour récupérer les différents statuts
        public function search_enum_fields($table1, $table2, $fields, $champs_statut)
        {
            
            /*$requete1 = $this->db->prepare("SHOW COLUMNS FROM article LIKE 'status'");
            $requete1->execute();
            $donnees = $requete1->fetchAll();
            $type = substr($donnees[0]['Type'], 6, -2);
            $liste_type = explode( "','", $type );
            $list_statut_present = array_values($liste_type);*/

            //$requete = $this->db->prepare("SELECT DISTINCT $fields FROM $table1 INNER JOIN $table2 ON $table1.id_$table1 = $table2.$champs_statut;");
            $requete = $this->db->prepare("SELECT $fields FROM $table1");
            $requete->execute();
            $list_statut_present = [];
            while($requete_enum = $requete->fetch(PDO::FETCH_ASSOC))
            {
                $list_statut_present[] = $requete_enum['name_' . $table1];
            }
        
            return $list_statut_present;
        }
        //permet de récupérer la connexion à la base de données
        public function setDb($db)
        {
            $this->db = $db;
        }
        
        /**
         * getSpecific is a method to request more specifics data where we select the columns, the conditions and the table.
         * return the array of fetched elements.
	     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
         * @param  mixed $cols
         *            Array with columns name to get.
         * @param  mixed $conditions
         *            Array of arrays to get the conditions WHERE. in each subArrays position 0 is the left member, position 1 is the right member.
         * @param  mixed $table
         *            Table where we perform the request.
         * @return void
         */
        public function getSpecific($cols, $conditions, $table) 
        {
            $values = array();
            $prepReq = "SELECT";
            foreach ($cols as $col) 
			{ 
			$prepReq = $prepReq." ".$col.","; 
			}
            $prepReq = substr_replace($prepReq ,"",-1) . " FROM " . $table; //remove last coma and add contents
			
            if(sizeof($conditions) != 0) 
			{
                $prepReq = $prepReq . " WHERE";
                foreach ($conditions as $condition) 
				{ 
                    $prepReq = $prepReq." ".$condition[0]." = ? and"; 
                    array_push($values, $condition[1]);
                }
                $prepReq = substr_replace($prepReq ,"",-4); //remove last " AND"
            }
			
            $req = $this->db->prepare($prepReq);
            $req->execute($values);
            $res = $req->fetchAll(PDO::FETCH_ASSOC);
            return $res;

        }

        /**
         * updateSpecific is a method to update more specifics data where we select the columns, the conditions and the table.
         * return true if insertion was a success, false if not.
	     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
         * @param  mixed $cols
         *            Array of arrays to get the conditions SET. in each subArrays position 0 is the left member, position 1 is the right member.
         * @param  mixed $conditions
         *            Array of arrays to get the conditions WHERE. in each subArrays position 0 is the left member, position 1 is the right member.
         * @param  mixed $table
         *            Table where we perform the request.
         * @return void
         */
        public function updateSpecific($cols, $conditions, $table) 
        {
            $values = array();
            $prepReq = "UPDATE ".$table." SET";
            foreach ($cols as $col) 
            { 
                $prepReq = $prepReq." ".$col[0]." = ?,"; 
                array_push($values, $col[1]);
            }
            $prepReq = substr_replace($prepReq ,"",-1); //remove last coma

            if(sizeof($conditions) != 0) 
            {
                $prepReq = $prepReq . " WHERE";
                foreach ($conditions as $condition) 
                { 
                    $prepReq = $prepReq." ".$condition[0]." = ? and"; 
                    array_push($values, $condition[1]);
                }
                $prepReq = substr_replace($prepReq ,"",-4); //remove last " AND"
            } 
            else 
            {  
                $prepReq = $prepReq . " WHERE 1"; 
            }

            $req = $this->db->prepare($prepReq);
            $res = $req->execute($values);
            return $res;

        }

        /**
         * insertSpecific is a method to update more specifics data where we select the columns, the conditions and the table.
         * return true if insertion was a success, false if not.
	     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
         * @param  mixed $cols
         *            Array of arrays to get the Columns and their values. in each subArrays position 0 is the left member, position 1 is the right member.
         * @param  mixed $table
         *            Table where we perform the request.
         * @return void
         */
        public function insertSpecific($cols, $table) 
        {
            $values = array();
            $prepReq = "INSERT INTO ".$table."(";
            foreach ($cols as $col) 
            { 
                $prepReq = $prepReq." ".$col[0].","; 
            }
            $prepReq = substr_replace($prepReq ,"",-1) . ")"; //remove last coma and add contents

            $prepReq = $prepReq . " VALUES(";
            foreach ($cols as $col) 
            { 
                $prepReq = $prepReq." ?,"; 
                array_push($values, $col[1]);
            }
            $prepReq = substr_replace($prepReq ,"",-1) . ")"; //remove last coma and add contents

            $req = $this->db->prepare($prepReq);
            $res = $req->execute($values);
            return $res;

        }

        /**
         * addHTMLXMLByPMCID is a special request to fetch the article xml from PMC and store it to the database
	     * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
         * @param  mixed $num_access
         * @param  mixed $pmcid
         * @return void
         */
        public function addHTMLXMLByPMCID($num_access, $pmcid) 
        {
            $pmcid = str_replace("PMC", "", $pmcid);
            $_GET['PMCID'] = $pmcid;
            $url = '../utils/fromPMCID/fromPMCID.php'; 
            $data = include($url);
            $cols = array();
            array_push($cols, array("html_xml", $data));
            $conditions = array();
            array_push($conditions, array("num_access", $num_access), array("pmcid", "PMC" . $pmcid));
            $this->updateSpecific($cols, $conditions, "article");
        }
        /**
         * deleteSpecific is a method to delete specifics data where we select the conditions and the table.
         * return the array of fetched elements.
         * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
         * @param  mixed $cols
         *            Array of arrays to get the conditions WHERE. in each subArrays position 0 is the left member, position 1 is the right member.    
         * @param  mixed $table
         *            Table where we perform the request.
         * @return void
         */
        public function deleteSpecific($cols, $table) 
        {
            $values = array();
            $prepReq = "DELETE FROM ".$table;
            if(sizeof($cols) != 0) 
            {
                $prepReq = $prepReq . " WHERE";
                foreach ($cols as $col) 
                { 
                    $prepReq = $prepReq." ".$col[0]." = ? and"; 
                    array_push($values, $col[1]);
                }
                $prepReq = substr_replace($prepReq ,"",-4); //remove last " AND"
            } 

            $req = $this->db->prepare($prepReq);
            $res = $req->execute($values);
            return $res;

        }
    }
    

?>