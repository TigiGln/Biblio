<?php
/*
Classes de connexion des utilisateurs 
*/
class ConnexionDB
{
    
    public $pdo;
    public $serveur;
    public $user;
    public $mdp;
    public $db;
    public $port;
    
    public function __construct($serveur,$db, $port, $user, $mdp)
    {
    
        $this->serveur = $serveur;
        $this->user = $user;
        $this->mdp = $mdp;
        $this->db = $db;
        $this->port = $port;

        $this->connexionBDD();
    }

    protected function connexionBDD()
    {
        try
        {
            //Change your port
            $this->pdo = new PDO("mysql:host=" . $this->serveur . ";port=" . $this->port . " ;dbname=" . $this->db, $this->user , $this->mdp, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //echo "Connexion réussi !";
        }
        catch (PDOException $e) // On attrape les exceptions PDOException.
        {
            echo '<div class="alert alert-danger" role="alert">Couldn`\'t connect to database. Please refer this issue to your administrator or your team.<br>[ERROR CODE: 500]</div>';
            echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); // On affiche le n° de l'erreur ainsi que le message.
        }
    }
    public function user()
    {
        return $this->user;
    }
    public function __sleep()
    {
        // Ici sont à placer des instructions à exécuter juste avant la linéarisation.
        // On retourne ensuite la liste des attributs qu'on veut sauver.
        return ['serveur', 'user', 'mdp', 'db', 'port'];
    }
    public function __wakeup()
    {
        $this->connexionBDD();
    }
}

?>