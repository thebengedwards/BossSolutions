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


$sqlProperty = "SELECT Property.propertyID, address1, address2, postcode, rent, bills, capacity, description
                 FROM Property
                 WHERE propertyID = $propertyID";
$queryResult = $dbConn->query($sqlProperty);



?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Delete Property</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>


<?php
$sqlCount = "SELECT COUNT(ID) AS TenantCount FROM Tenant WHERE PropertyID = $propertyID";
$countResult = $dbConn->query($sqlCount);
$tenants = $countResult->fetch(PDO::FETCH_ASSOC);
$count = $tenants['TenantCount'];
if($count <= 0 ) {
    if (@!$landlordID) {
        try {
            header('Location:PropertyAdmin.php');
            $SQLinsert = "INSERT INTO PropertyRecovery(propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description) 
                    SELECT propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description FROM Property WHERE Property.propertyID = '$propertyID'";
            $SQLInsertImage = "INSERT INTO PropertyImagesRecovery(imageID, name, image, propertyID, type) 
                    SELECT imageID, name, image, propertyID, type FROM PropertyImages WHERE PropertyImages.propertyID = '$propertyID'";
            $dbConn->exec($SQLinsert);
            $dbConn->exec($SQLInsertImage);
            $sqlDelete = "DELETE FROM Property WHERE propertyID = '$propertyID'";
            $sqlImageDelete = "DELETE FROM PropertyImages WHERE propertyID = '$propertyID'";
            $dbConn->exec($sqlDelete);
            $dbConn->exec($sqlImageDelete);
            echo "<p>Property Deleted Successfully </p>\n";
            echo "<p>Go back to your property list <a href='PropertyAdmin.php'>here</a>.</p>\n";
        } catch
        (Exception $e) {
            //echo"$SQLinsert";
            $e->getMessage();
            echo "<p>Please <a href='PropertyAdmin.php'>Try Again</a>.</p>\n";
        }
    } else {
        try {
            header('Location:PropertyLandlord.php');
            $SQLinsert = "INSERT INTO PropertyRecovery(propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description) 
                    SELECT propertyID, address1, address2, postcode, rent, bills, landlordID, capacity, description FROM Property WHERE Property.propertyID = '$propertyID'";
            $SQLInsertImage = "INSERT INTO PropertyImagesRecovery(imageID, name, image, propertyID, type) 
                    SELECT imageID, name, image, propertyID, type FROM PropertyImages WHERE PropertyImages.propertyID = '$propertyID'";
            $dbConn->exec($SQLinsert);
            $dbConn->exec($SQLInsertImage);
            $sqlDelete = "DELETE FROM Property WHERE propertyID = '$propertyID'";
            $sqlImageDelete = "DELETE FROM PropertyImages WHERE propertyID = '$propertyID'";
            $dbConn->exec($sqlDelete);
            $dbConn->exec($sqlImageDelete);

            echo "<p>Property Deleted Succesfully </p>\n";
            echo "$count";
            echo "<p>Go back to your property list <a href='PropertyLandlord.php'>here</a>.</p>\n";
        } catch (Exception $e) {
            echo "<p>Deletion Unsuccessful - There must be no Tenants assigned to the property you are deleting. <br>" . $e->getMessage() . "</p>\n";
            echo "<p><a href='PropertyLandlord.php'>Try Again</a>.</p>\n";
            echo "$count";
        }
    }
} else {
    echo "<p>Deletion Unsuccessful - There must be no Tenants assigned to the property you are deleting.";
}
?>

</body>
</html>
