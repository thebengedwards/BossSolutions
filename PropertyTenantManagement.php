<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();

require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();
turnButtGreen('propertyMan');
?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>Tenant Management</title>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <link rel='icon' type='image/x-icon' href='favicon.ico' />
    </head>
<body>

    <h1> Property Management </h1>

<?php
require_once("functions.php");
$dbConn = getConnection();
$session = $_SESSION['username'];


$propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;
// if the Property selected doesnt match a PropertyID from the database display error message.
if (empty($propertyID)) {
    echo "<p>Please <a href='PropertyLandlord.php'>choose</a> a property.</p>\n";
} else {
    try {
        $capacity = filter_has_var(INPUT_GET, 'capacity') ? $_GET['capacity'] : null;
        require_once("functions.php");
        $dbConn = getConnection();
// retrieving the information from phpmyadmin that is required for php
        $sqlProperty = "SELECT propertyID, address1, address2, postcode, capacity
                 FROM Property
                 WHERE propertyID = $propertyID";
        $propertyResult = $dbConn->query($sqlProperty);

        echo"<h2>Property Selected:</h2>";
        echo"<div class = 'propertySelected'>";
        while ($rowObj = $propertyResult->fetchObject()) {
            echo "<div class='propertySelectedList'>\n
				   <span class='propertyID'>Property ID: {$rowObj->propertyID}</a></span><br>
				   <span class='Address 1'>Address 1: {$rowObj->address1}</span><br>
				   <span class='Address 2'>Address 2: {$rowObj->address2}</span><br>
				   <span class='Postcode'>Postcode: {$rowObj->postcode}</span><br>
				   <span class='Capacity'>Maximum Capacity: {$rowObj->capacity}</span><br><br>
				   </div></div>
				   ";
        }
        echo"</div>";

        echo"<h2>Current Tenants</h2>";
        $sqlCurrent = "SELECT Tenant.ID, firstName, lastName, username, Tenant.accountID, Tenant.PropertyID, capacity , email, phoneNumber
        FROM Tenant
        JOIN Account
        ON Tenant.accountID = Account.accountID
        JOIN Property
        ON Property.propertyID = Tenant.PropertyID
        HAVING Tenant.PropertyID = $propertyID";
        $currentResult = $dbConn->query($sqlCurrent);
        echo"<div class = 'PropertyTenant'>";
        while ($rowObj = $currentResult->fetchObject()) {
            echo "<div class='PropertyTenantList'>\n
				   <span class='tenantID' style='display:none'>Tenant ID: {$rowObj->ID}</a></span>
				   <span class='username'>Username: {$rowObj->username}</span><br>
				   <span class='firstName'>First Name: {$rowObj->firstName}</span><br>
				   <span class='lastName'>Surname: {$rowObj->lastName}</span><br>
				   Contact Information<br>
				   <span class='email'>Email Address: {$rowObj->email}</span><br>
				   <span class='phoneNumber'>Phone Number : {$rowObj->phoneNumber}</span><br>
				   <span class='propertyID'><a href='PropertyRemoveTenant.php?tenantID={$rowObj->ID}'>Remove Tenant</a></span><br>
				   </div>";
        }
        echo"</div>";
        $sqlCapacity = $dbConn->prepare("SELECT capacity FROM Property WHERE propertyID = $propertyID");
        $sqlCapacity->execute();
        $capacity = $sqlCapacity->fetchColumn();
        //echo"$capacity <br>";

        $sqlCount = $dbConn->prepare("SELECT COUNT(PropertyID) FROM Tenant WHERE PropertyID = $propertyID");
        $sqlCount->execute();
        $tenantCount = $sqlCount->fetchColumn();
        //echo"$tenantCount <br>";

        echo "<h2> Assign Tenants</h2>";
        if($tenantCount < $capacity) {
            $sqlAssign = "SELECT ID, firstName, lastName, username, Tenant.accountID, PropertyID
            FROM Tenant
            JOIN Account
            ON Account.accountID = Tenant.accountID
            WHERE Tenant.PropertyID IS NULL";
            $assignResult = $dbConn->query($sqlAssign);
            //echo "$sqlTenant";

            if ($assignResult) {
                echo "<form class='Assign' action='PropertyAssignTenant.php'>";
                echo "<select name=username value=''>Tenant Username</option>";
                echo " <option value=''> Select Tenant </option>";
                foreach ($dbConn->query($sqlAssign) as $row) {//Array or records stored in $row
                    echo "<option value=$row[ID]>$row[username]</option>";
                    /* Option values are added by looping through the array */
                }
                echo "</select>";// Closing of list box
                echo "<input type='hidden' value='$propertyID' READONLY name='propertyID'>";

                echo "<p><input type='submit' name='submit' value='Assign Tenant'></p> </form>";
            } else {
                echo "There are currently no tenants living in this property.";
            }
        } else {
            echo "Property is currently at maximum capacity";
        }
    } catch (Exception $e) {
        echo "<p>Property details not found: " . $e->getMessage() . "</p>\n";
        echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
    }

}
?>