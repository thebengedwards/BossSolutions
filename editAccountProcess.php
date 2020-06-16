<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <link rel=meta charset='UTF-8' />
    <link rel='stylesheet' type='text/css' href='stylesheet.css'>
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
    <title>Account Edit Process</title>
</head>
<body>

<?php

$accountID = filter_has_var(INPUT_POST, 'accountID') ? $_POST['accountID']: null;
$accountID = trim($accountID);
$username = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
$username = trim($username);
$passwordHash = filter_has_var(INPUT_POST, 'passwordHash') ? $_POST['passwordHash']: null;
$passwordHash = trim($passwordHash);
$hashed_password = password_hash($passwordHash, PASSWORD_DEFAULT);
$firstName = filter_has_var(INPUT_POST, 'firstName') ? $_POST['firstName']: null;
$firstName = trim($firstName);
$lastName = filter_has_var(INPUT_POST, 'lastName') ? $_POST['lastName']: null;
$lastName = trim($lastName);
$email = filter_has_var(INPUT_POST, 'email') ? $_POST['email']: null;
$email = trim($email);
$phoneNumber = filter_has_var(INPUT_POST, 'phoneNumber') ? $_POST['phoneNumber']: null;
$phoneNumber = trim($phoneNumber);
$age = filter_has_var(INPUT_POST, 'age') ? $_POST['age']: null;
$age = trim($age);
$gender = filter_has_var(INPUT_POST, 'gender') ? $_POST['gender']: null;
$gender = trim($gender);

$editorUsername = $_SESSION['username'];

require_once("functions.php");
$dbConn = getConnection();
    
$querySQL = "SELECT accountID FROM Account WHERE username='$editorUsername'";
$queryResult = $dbConn->query($querySQL);

while($rowObj=$queryResult->fetchObject())
{
    if($rowObj->accountID == $accountID)//if editing own account
    {
        if(empty($username) or empty($passwordHash) or empty($email))
        {
            header('Location: editAccount.php');
        }
        else
        {
            require_once("functions.php");
            $dbConn = getConnection();

            $querySQL = "UPDATE Account SET username = '$username', passwordHash = '$hashed_password', firstName = '$firstName', lastName = '$lastName', email = '$email', phoneNumber = '$phoneNumber', age = '$age', gender = '$gender' WHERE accountID='$accountID'";
            $stmt = $dbConn->query($querySQL);

            $_SESSION['loggedIn'] = false;
            header('Location: index.php');
        }
    }
    else if($rowObj->accountID !== $accountID)//if editing someone elses account
    {
        if(empty($username) or empty($passwordHash) or empty($email))
        {
            header('Location: editAccount.php');
        }
        else
        {
            require_once("functions.php");
            $dbConn = getConnection();

            $querySQL = "UPDATE Account SET username = '$username', passwordHash = '$hashed_password', firstName = '$firstName', lastName = '$lastName', email = '$email', phoneNumber = '$phoneNumber', age = '$age', gender = '$gender' WHERE accountID='$accountID'";
            $stmt = $dbConn->query($querySQL);

            header('Location: adminAccMan.php');
        }
    }
}
?>

</body>
</html>