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
    <title>Account Deletion Process</title>
</head>
<body>

<?php

$username = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
$username = trim($username);
$deleteAccountID = filter_has_var(INPUT_POST, 'accountID') ? $_POST['accountID']: null;
$deleteAccountID = trim($deleteAccountID);

require_once('functions.php');
$dbConn = getConnection();
$querySQL = "DELETE FROM Account WHERE accountID ='$deleteAccountID'";
$queryResult = $dbConn->query($querySQL);

header('Location: index.php');

?>

</body>
</html>