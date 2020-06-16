<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Delete Image</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>

<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();

require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();

$dbConn = getConnection();
$session = $_SESSION['username'];

$querySQL = "SELECT Account.accountID, Landlord.landlordID
                 FROM Account 
                 INNER JOIN Landlord
                 ON Account.accountID = Landlord.accountID
                 WHERE username = '$session'";
$stmt = $dbConn->query($querySQL);
$stmt->execute(array(':username' => $session));
$user = $stmt->fetchObject();

if(@$user->landlordID) {
    @$accountID = $user->accountID;
    @$landlordID = $user->landlordID;
}

$imageID = filter_has_var(INPUT_GET, 'imageID') ? $_GET['imageID'] : null;

if(@empty(@$landlordID)) {
    try {
        $sqlDelete = "DELETE FROM PropertyImages WHERE imageID = '$imageID'";
        $dbConn->exec($sqlDelete);

        echo "<p>Property Image Deleted Succesfully </p>\n";
        echo "<p>Go back to the property list <a href='PropertyAdmin.php'>here</a>.</p>\n";
    } catch (Exception $e) {
        //echo"$SQLinsert";
        echo "<p>Deletion Unsuccessful - There must be no Tenants assigned to the property you are deleting. <br>" . $e->getMessage() . "</p>\n";
        echo "<p>Please <a href='PropertyAdmin.php'>Try Again</a>.</p>\n";
    }
} else {
    try {
        $sqlDelete = "DELETE FROM PropertyImages WHERE imageID = '$imageID'";
        $dbConn->exec($sqlDelete);

        echo "<p>Property Image Deleted Succesfully </p>\n";
        echo "<p>Go back to your property list <a href='PropertyLandlord.php'>here</a>.</p>\n";
    } catch (Exception $e) {
        echo "<p>Deletion Unsuccessful - There must be no Tenants assigned to the property you are deleting. <br>" . $e->getMessage() . "</p>\n";
        echo "<p><a href='PropertyLandlord.php'>Try Again</a>.</p>\n";
    }
}
?>

</body>
</html>
