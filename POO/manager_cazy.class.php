<?php
class ManagerCazy
{
    public $pdo;
    public $serveur;
    public $db;
    public $name;
    public $password;

    public function __construct($serveur, $db, $name, $password)
    {
        $this->serveur = $serveur;
        $this->db = $db;
        $this->name = $name;
        $this->password = $password;
    
        $this->connexionDb();
        
    }
    //Permet de se connecter à la base de données
    protected function connexionDb()
    {
        try
        {
            $this->pdo = new PDO("mysql:host=" . $this->serveur . ";dbname=" . $this->db, $this->name , $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //echo "Connexion réussi !";
        }
        catch (PDOException $e) // On attrape les exceptions PDOException.
        {
        echo 'La connexion a échoué.<br />';
        echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); // On affiche le n° de l'erreur ainsi que le message.
        }
    }
    //méthodes sleep permet lors de la linéairisation de l'objet dans les variable de session de stocker l'information pour la délinéarisation
    public function __sleep()
    {
        // Ici sont à placer des instructions à exécuter juste avant la linéarisation.
        // On retourne ensuite la liste des attributs qu'on veut sauver.
        return ['serveur', 'db', 'name', 'password'];
    }
    //méthode wakup lors de la délinéarisation pour recréer la connexion à la bdd
    public function __wakeup()
    {
        $this->connexionDb();
    }
    public function get_numAccess($pub_db_acc, $table)
    {
        $list_pub_db_access_cazy = [];
        $request = $this->pdo->prepare("SELECT $pub_db_acc FROM $table WHERE pub_db = 'pubmed'");
        $request->execute();
        while($data = $request->fetch(PDO::FETCH_ASSOC))
        {
            $list_pub_db_access_cazy[] = $data ["$pub_db_acc"];
        }
        return $list_pub_db_access_cazy;
    }
    public function get_entryid($db_acc)
    {
        $request = $this->pdo->prepare("SELECT entry_id FROM annotation WHERE db_acc = '$db_acc'");
        $request->execute();
        $data = $request->fetch();
        if (!empty($data))
        {
            return $data['entry_id'];
        }
        else
        {
            return $data = '';
        }
    }

    public function get($base, $columns, $array_tables, $champ_where, $value_where, $champ_join1="", $champ_join2="", $champ_table3="", $champ_where2=1, $value_where2=1)
    {
        if(count($array_tables) == 1 AND count($base) == 1)
        {
            $table1 = $array_tables[0];
            $base1 = $base[0];
            $request = $this->pdo->prepare("SELECT $columns FROM $base1.$table1 WHERE $champ_where = '$value_where' AND $champ_where2 = $value_where2");
        }
        else if(count($array_tables) == 2 AND count($base) == 2)
        {
            $table1 = $array_tables[0];
            $table2 = $array_tables[1];
            $base1 = $base[0];
            $base2 = $base[1];
            $request = $this->pdo->prepare("SELECT $columns FROM $base1.$table1 INNER JOIN $base2.$table2 ON $base1.$table1.$champ_join1 = $base2.$table2.$champ_join1 WHERE $champ_where = $value_where AND $champ_where2 = $value_where2");
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
            $request = $this->pdo->prepare("SELECT $columns FROM $base1.$table1 INNER JOIN $base2.$table2 ON $base1.$table1.$champ_join1 = $base2.$table2.$champ_join1 INNER JOIN $base3.$table3 ON $base2.$table2.$champ_join2 = $base3.$table3.$champ_join2 WHERE $champ_where = $value_where AND $champ_where2 = $value_where2"); 
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
}
?>