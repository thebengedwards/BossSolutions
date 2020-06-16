<?php
/**
 * Created by PhpStorm.
 * User: calumwillis
 * Date: 13/02/2020
 * Time: 18:51
 */

ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
require_once("functions.php");

validateLogIn();

list($input, $errors) = validateLogIn();
if ($errors) {
    checkLogin($_SESSION['redirect']);
    foreach ($errors as $error) {
        echo $error;
    }
}
else {
    echo "Logged in";
    //header('Location: '.$_SESSION['redirect'].'.php');
}

function validateLogIn(){
    $errors = array();
    $input = array();

       $input['username'] = filter_has_var(INPUT_POST, 'username')
           ? $_POST['username'] : null;
       $input['password'] = filter_has_var(INPUT_POST, 'password')
           ? $_POST['password'] : null;
       $input['username'] = strtolower($input['username']);
    try {

        $dbConn = getConnection();

        $querySQL = "SELECT passwordHash
                 FROM Account
                 WHERE username = :username";

        $stmt = $dbConn->prepare($querySQL);
        $stmt->execute(array(':username' => $input['username']));

        $user = $stmt->fetchObject();
        if ($user) {
            $passwordHash = $user->passwordHash;
            if (password_verify($input['password'], $passwordHash))
            {

                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $input['username'];

                $dbConn = getConnection();
                $querySQL = "SELECT accountID
                 FROM Account
                 WHERE username = :username";
                $stmt = $dbConn->prepare($querySQL);

                $stmt->execute(array(':username' => $input['username']));

                $_SESSION['userID'] = $stmt->fetchColumn();
            }
            else
                {
                $errors[] = "The Username or Password was incorrect.";
                $_SESSION['loggedIn'] = false;
            }
        } else {
            $errors[] = "The Username or Password was incorrect.";
            $_SESSION['loggedIn'] = false;
        }
        header('Location: ' . $_SESSION['redirect'] . '.php');


    } catch (Exception $e) {
        echo "A problem occurred. Please try again.";
    }
    return array($input, $errors);}

?>
