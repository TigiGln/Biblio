<?php
/*
 * Created on Mon Apr 19 2021
 * Latest update on Fri Apr 7 2021
 * Info - index page
 * @author Eddy Ikhlef <eddy.ikhlef@protonmail.com>
 * @author Thierry Galliano
 */
require('./views/header.php');
?>

<link href="./css/signIn.css" rel="stylesheet">
<main class="form-signin">
  <form name="signInForm">
    <div class="text-center">
      <img src="./pictures/logo_big.png" width="150">
      <br>
      <h1>Outil Biblio</h1>
    </div>
    <br>
<?php
require("./POO/class_saveload_strategies.php");
$saveload = new SaveLoadStrategies($position, $manager);
if(isset($_GET['loginValue']) && isset($_GET['password'])) 
{
    /* check if loginValue exist */
    if(filter_var($_GET['loginValue'], FILTER_VALIDATE_EMAIL)) 
    {
        $cols = array(); 
        array_push($cols, "email");
        $conditions = array(); 
        array_push($conditions, array("email", $_GET['loginValue']));
    } 
    else 
    {
        $cols = array(); array_push($cols, "name_user");
        $conditions = array(); 
        array_push($conditions, array("name_user", $_GET['loginValue']));
    }
    if(empty($saveload->loadAsDB("user", $cols, $conditions, null))) 
    {
        $GLOBALS['connectionError'] = "Unknown email or username, please retry.";
    } 
    /* else we can check if user is correct */
    else 
    {
        $cols = array(); 
        array_push($cols, "id_user", "email", "name_user", "password");
        if(filter_var($_GET['loginValue'], FILTER_VALIDATE_EMAIL)) 
        {
            $conditions = array(); 
            array_push($conditions, array("email", $_GET['loginValue']));
        } 
        else 
        {
            $conditions = array(); 
            array_push($conditions, array("name_user", $_GET['loginValue']));
        }
        $res = $saveload->loadAsDB("user", $cols, $conditions, null);
        if(empty($res) || !password_verify($_GET['password'], $res[0]['password'])) 
        {
            $GLOBALS['connectionError'] = "Wrong password, please retry: ".password_verify($_GET['password'], $res[0]['password']);
        } 
        else 
        {
            $_SESSION['connexiondb'] = $saveload->connexiondb();
            $_SESSION['connexion'] = $res[0]['name_user'];
            $_SESSION['username'] = $res[0]['name_user'];
            $_SESSION['userName'] = $res[0]['name_user'];
            $_SESSION['userID'] = $res[0]['id_user'];
            if(isset($_GET['rememberMe'])) 
            {
                $userConnection->generateCookie(array($res[0]['id_user'], $res[0]['name_user']));
            }
            header('Location: ./');
        }
    }
}
/* print connection error message */
if(isset($GLOBALS['connectionError'])) 
{
    echo '<div class="alert alert-danger" role="alert">'.$GLOBALS['connectionError'].'</div>';
}
?>
    <form method="get" action="form_connection.php">
        <p class="form-floating">
            <input class="form-control" type="text" name="loginValue" id="email" required="required"> 
            <label for="name_user">Email or Username</label>
        </p>
        <p class="form-floating">
            <input class="form-control" type="password" name="password" id="password" required="required">
            <label for="password">Password</label>
        </p>
        <button class="w-100 btn btn-lg btn-outline-primary" type="submit">Connect</button>
        <p class="checkbox_mb-3 text-center">
            <input type="checkbox" value="rememberMe" name="rememberMe"> Connect me Automatically
        </p>
    </form>
</main>

<?php
require('./views/footer.php');
?>