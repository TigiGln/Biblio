<?php
  //Checking the presence of classes to launch the connection to the database.
  if(!class_exists("ConnexionDB")) 
  {
    require($position . "/POO/class_connexion.php");
  }
  if(!class_exists("Manager"))
  { 
    require($position . "/POO/class_manager_bd.php");
  }
  if(!class_exists("UserConnection")) 
  {
    require($position . "/POO/class_userConnection.php");
  }
  if(!class_exists("UserConnection"))
  {
    require($position . "/POO/class_main_menu.php");
  }
  $userConnection = new UserConnection(true);
  //$userConnection->isValid();
  //If the connection to the database is in the cookie you run the manager
  
  if(isset($_SESSION['connexiondb']->pdo)) 
  { 
    $manager = new Manager($_SESSION["connexiondb"]->pdo); 
  }   
  else //Else you reconnect to the database before
  {
    $_SESSION['connexiondb'] = new ConnexionDB("localhost", "litterature", "3308", "root", "");
    $manager = new Manager($_SESSION['connexiondb']->pdo);
  }
  $userConnection->isValid($manager);

?>