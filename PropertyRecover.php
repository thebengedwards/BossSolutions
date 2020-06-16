<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Property Recovery</title>
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

$propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;

$dbConn = getConnection();
$sqlProperty = "SELECT Property.propertyID, address1, address2, postcode, rent, bills, capacity, description
                 FROM Property
                 WHERE propertyID = $propertyID";
$queryResult = $dbConn->query($sqlProperty);


if (@!$landlordID) {
    try {
        header('Location:PropertyAdmin.php');
        $SQLinsert = "INSERT INTO Property(propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description) 
                    SELECT propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description
                    FROM PropertyRecovery WHERE propertyID = '$propertyID'";
        $SQLInsertImage = "INSERT INTO PropertyImages(imageID, name, type, image, propertyID) 
                    SELECT imageID, name, type, image, propertyID
                    FROM PropertyImagesRecovery WHERE propertyID = '$propertyID'";
        $dbConn->exec($SQLinsert);
        $dbConn->exec($SQLInsertImage);
        $sqlDelete = "DELETE FROM PropertyRecovery WHERE propertyID = '$propertyID'";
        $sqlImageDelete = "DELETE FROM PropertyImagesRecovery WHERE propertyID = '$propertyID'";
        $dbConn->exec($sqlDelete);
        $dbConn->exec($sqlImageDelete);


        echo "<p>Property Recovery Successful </p>\n";
        echo "<p>Go back to your property list <a href='PropertyAdmin.php'>here</a>.</p>\n";
    } catch (Exception $e) {
        echo"$SQLinsert";
        echo "<p>Recovery Unsuccessful" . $e->getMessage() . "</p>\n";
        echo "<p><a href='PropertyAdmin.php'>Try Again</a>.</p>\n";
    }
} else {
    try {
        header('Location:PropertyLandlord.php');
        $SQLinsert = "INSERT INTO Property(propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description) 
                    SELECT propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description 
                    FROM PropertyRecovery WHERE propertyID = '$propertyID'";
        $SQLInsertImage = "INSERT INTO PropertyImages(imageID, name, type, image, propertyID) 
                    SELECT imageID, name, type, image, propertyID 
                    FROM PropertyImagesRecovery WHERE propertyID = '$propertyID'";
        $dbConn->exec($SQLinsert);
        $dbConn->exec($SQLInsertImage);
        $sqlDelete = "DELETE FROM PropertyRecovery WHERE propertyID = '$propertyID'";
        $sqlImageDelete = "DELETE FROM PropertyImagesRecovery WHERE propertyID = '$propertyID'";
        $dbConn->exec($sqlDelete);
        $dbConn->exec($sqlImageDelete);

        echo "<p>Property Recovered Successful </p>\n";
        echo "<p>Go back to your property list <a href='PropertyLandlord.php'>here</a>.</p>\n";
    } catch (Exception $e) {
        echo "<p>Recovery Unsuccessful" . $e->getMessage() . "</p>\n";
        echo "<p><a href='PropertyLandlord.php'>Try Again</a>.</p>\n";
    }
}
?>

</body>
</html>
