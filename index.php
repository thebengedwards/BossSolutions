<?php
/**
 * Created by PhpStorm.
 * User: calumwillis
 * Date: 13/02/2020
 * Time: 17:25
 */

ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>
<?php
require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();
turnButtGreen('index');
$dbConn = getConnection();


echo "<h1> Available Properties </h1>";

try {

    $sqlAvailable = "SELECT Property.propertyID, address1, address2, postcode, rent, bills, capacity, description
            FROM Property
            JOIN PropertyImages
            ON PropertyImages.propertyID = Property.propertyID
            WHERE NOT EXISTS (SELECT * FROM Tenant WHERE Tenant.propertyID = Property.propertyID) && type = 'Outside - Front View'";
    $availableResult = $dbConn->query($sqlAvailable);
    //echo "$sqlTenant";

    echo "<div class='indexPropertyDiv' >";
    while ($rowObj = $availableResult->fetchObject()) {
        echo "<div class='indexPropertyDetails'>";
        echo "<span class='Address 1'><a href='Property.php?propertyID={$rowObj->propertyID}'>{$rowObj->address1}, {$rowObj->address2}</a></span><br>
              <span class='postcode'>Postcode: {$rowObj->postcode}</span><br>
			  <span class='Rent'>Rent: £{$rowObj->rent} pp/pm</span>
			  <span class='Bills'>Bills: £{$rowObj->bills} pp/pm</span><br>
			  <span class='capacity'>Property Capacity: {$rowObj->capacity}</span><br>
			  <span class='propertyID' style='display:none' value='{$rowObj->propertyID}'></span><br>";

        $propertyID = $rowObj->propertyID;
        $sqlPropertyImages = "SELECT name, type FROM PropertyImages WHERE type = 'Outside - Front View' 
                && NOT EXISTS (SELECT * FROM Tenant WHERE Tenant.propertyID = PropertyImages.propertyID) && propertyID = '$propertyID'";
        $imageResult = $dbConn->query($sqlPropertyImages);
        foreach ($dbConn->query($sqlPropertyImages) as $row) {
            echo "<div class = 'indexPropertyImage'>
                  <a href='Property.php?propertyID={$rowObj->propertyID}'>
                  <img class ='view' src = PropertyImages/$row[name]>
                  </a>
                  </div><br>";
        }

        echo "<span class='description'>{$rowObj->description}</span><br>";

        $sqlLandlord = $dbConn->prepare("SELECT landlordID FROM Property WHERE propertyID = $propertyID");
        $sqlLandlord->execute();
        $landlordID = $sqlLandlord->fetchColumn();

        $sqlAccountID = $dbConn->prepare("SELECT accountID FROM Landlord WHERE landlordID = $landlordID");
        $sqlAccountID->execute();
        $landlordAccountID = $sqlAccountID->fetchColumn();


        $sqlLandlordAccount = "SELECT username, firstName, lastName, email, phoneNumber
                 FROM Account
                 JOIN Landlord
                 ON Landlord.accountID = Account.accountID
                 WHERE Account.accountID = $landlordAccountID";
        $landlordAccountResult = $dbConn->query($sqlLandlordAccount);

        echo "<br><div class='indexLandlordDetails' >";
        echo "Landlord: ";
        while ($rowObj = $landlordAccountResult->fetchObject()) {
            echo "<span class='username' style='display:none'> Username: {$rowObj->username}</span><br>
                  <span class='firstName'>First Name: {$rowObj->firstName}</span><br>
                  <span class='lastName'>Surname: {$rowObj->lastName}</span><br>
                  <span class='email'>Email Address: {$rowObj->email}</span><br>
				  <span class='phoneNumber'>Phone Number: {$rowObj->phoneNumber}</span><br>
				   </div><br>";
        }
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<p>Property details not found: " . $e->getMessage() . "</p>\n";
    echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
}

?>
<br clear="all" />
</body>
</html>

