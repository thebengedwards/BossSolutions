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
    <title>Tenant Creation Process</title>
</head>
<body>

<?php

$username = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
$username = trim($username);
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
$password = filter_has_var(INPUT_POST, 'password') ? $_POST['password']: null;
$password = trim($password);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$date = date('Y-m-d H:i:s');

require_once("functions.php");
$dbConn = getConnection();

$querySQL = "INSERT INTO Account (username, passwordHash, firstName, lastName, email, phoneNumber, age, gender, creationDate) VALUES ('$username', '$hashed_password', '$firstName', '$lastName', '$email', '$phoneNumber', '$age', '$gender', '$date')";
$stmt = $dbConn->query($querySQL);

if($stmt) {
    
    $querySQL = "SELECT accountID FROM Account WHERE email = '$email'";
    $stmt = $dbConn->query($querySQL);
    
    $stmt -> execute(array(':email'=>$email));
    $user = $stmt->fetchObject();

    if($user){

        $accountID = $user->accountID;
        
        $querySQL = "INSERT INTO Tenant (accountID) VALUES ('$accountID')";
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