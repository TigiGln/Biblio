<?php
  //Checking the presence of classes to launch the connection to the database.
  if(!class_exists("ConnexionDB")) 
  {
    require($position."/POO/class_connexion.php");
  }
  if(!class_exists("Manager"))
  { 
    require($position."/POO/class_manager_bd.php");
  }
  if(!class_exists("UserConnection")) 
  {
    require($position."/POO/class_userConnection.php");
  }
  if(!class_exists("UserConnection"))
  {
    require($position."/POO/class_main_menu.php");
  }
  $userConnection = new UserConnection(true);
  //If the connection to the database is in the cookie you run the manager
  if(isset($_SESSION['connexiondb'])) 
  { 
    $manager = new Manager($_SESSION["connexiondb"]->pdo); 
  }   
  else //Else you reconnect to the database before
  {
    $_SESSION['connexiondb'] = new ConnexionDB("localhost", "biblio", "3306", "thierry", "Th1erryG@llian0");
    $manager = new Manager($_SESSION['connexiondb']->pdo);
  }
  $search_article = $manager->db->query("SELECT * FROM article");
  $nb_article = $search_article->rowCount();
  $userConnection->isValid($nb_article);

?>