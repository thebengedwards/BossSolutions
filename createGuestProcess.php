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
    <title>Guest Creation Process</title>
</head>
<body>

<?php

$username = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
$username = trim($username);
$email = filter_has_var(INPUT_POST, 'email') ? $_POST['email']: null;
$email = trim($email);
$password = filter_has_var(INPUT_POST, 'password') ? $_POST['password']: null;
$password = trim($password);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$date = date('Y-m-d H:i:s');

require_once("functions.php");
$dbConn = getConnection();

$querySQL = "INSERT INTO Account (username, email, passwordHash, creationDate) VALUES ('$username', '$email', '$hashed_password','$date')";
$stmt = $dbConn->query($querySQL);

if($stmt) {
    
    $querySQL = "SELECT accountID FROM Account WHERE email = '$email'";
    $stmt = $dbConn->query($querySQL);
    
    $stmt -> execute(array(':email'=>$email));
    $user = $stmt->fetchObject();

    if($user){

        $accountID = $user->accountID;
        
        $querySQL = "INSERT INTO Guest (accountID) VALUES ('$accountID')";
        $stmt = $dbConn->query($querySQL);

        header('Location: index.php');

    } else {
        echo"Error, see: $querySQL";
        header('Location: index.php');
    }

} else {
    echo"Error, see: $querySQL";
    header('Location: index.php');
}

?>

</body>
</html>