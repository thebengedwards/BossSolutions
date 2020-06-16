<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Admin Property Management</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico'/>
</head>
<body>
<div class=wrapper>
    <?php
    require_once("functions.php");
    checkLogin('index');
    buildNav();
    buildFooter();
    turnButtGreen('propertyMan');

    $dbConn = getConnection();
    $session = $_SESSION['username'];

    ?>
    <h1> Property Management </h1>

    <h2>All Properties</h2>


    <?php
    if ($_SESSION['loggedIn'] == true) {
    if (checkPermission('Admin') == true) {
    try {
        require_once("functions.php");
        $dbConn = getConnection();

        $sqlLandlord = "SELECT landlordID, username
        FROM Landlord
        JOIN Account
        ON Account.accountID = Landlord.accountID";
        $landlordResult = $dbConn->query($sqlLandlord);

// retrieving the information from phpmyadmin that is required for php
        $propertyQuery = "SELECT propertyID, address1, address2, postcode, rent, bills, capacity, description, firstName, lastName, username
                 FROM Account
                 JOIN Landlord 
                 ON Landlord.accountID = Account.accountID
                 JOIN Property
                 ON Property.landlordID = Landlord.landlordID
                 ORDER BY Property.propertyID";

        $propertyResult = $dbConn->query($propertyQuery);

        echo "<div class = 'myProperties'>";
        while ($rowObj = $propertyResult->fetchObject()) {
            echo "<div class='propertyList'>
                   <span class='landlord1'>Landlord: {$rowObj->firstName} {$rowObj->lastName} ({$rowObj->username}) </span><br>
				   <span class='PropertyID' style='display:none'>{$rowObj->propertyID}</span>
				   <span class='address1'>Address: {$rowObj->address1}, {$rowObj->address2}</span><br>
				   <span class='postcode'>Postcode: {$rowObj->postcode}</span><br>
				   <span class='rent'>Rent: £{$rowObj->rent}</span><br>
				   <span class='bills'>Bills: £{$rowObj->bills}</span><br>
				   <span class='capacity'>Capacity: {$rowObj->capacity}</span><br><br>
				   <span class='description' style='display:none'>{$rowObj->description}</span>
			       <span class='manageProperty'><a href='PropertyEdit.php?propertyID={$rowObj->propertyID}'>Edit Property</a></span>
				   <span class='assignProperty'><a href='PropertyTenantManagement.php?propertyID={$rowObj->propertyID}'>Manage Tenant</a></span>
				   <span class='deleteProperty'><a href='PropertyDelete.php?propertyID={$rowObj->propertyID}'>Delete Property</a></span>
				   </div>";
        }
    } catch (Exception $e) {
        echo "<p>Query failed: " . $e->getMessage() . "</p>\n";
    }
    echo "</div>";
    ?>

    <div class="createProperty">
        <h1>Create Property</h1>
        <form class="newPropertyDetails" action="PropertyInsert.php" method="post">
            <?php
            echo "<label>Landlord: </label> <select name=username value=''>Landlord Username</option>"; // list box select command
            foreach ($dbConn->query($sqlLandlord) as $row) {//Array or records stored in $row
                echo "<option value=$row[landlordID]>$row[username]</option>";
                /* Option values are added by looping through the array */
            }
            echo "</select>
<br>
    <label>Address 1: </label>
    <input type='text' placeholder='Address 1' name='address1' id='address1' required>

    <label>Address 2: </label>
    <input type='text' placeholder= 'Address 2' name= 'address2' id= 'address2'><br>

    <label>Postcode: </label>
    <input type='text' placeholder='Postcode' name='postcode' id='postcode' required><br>

    <label>Rent: (pp/pm) </label>
    <input type='text' placeholder='Rent' name='rent' id='rent' required><br>

    <label>Bills: (pp/pm) </label>
    <input type='text' placeholder= 'Bills' name= 'bills' id= 'bills' required><br>

    <label>Maximum Tenants: </label>
    <input type='text' placeholder= 'Maximum Capacity' name= 'capacity' id= 'capacity' required><br>
    
    <label>Description of Property</label><br>
    <textarea rows='8' cols='60' name= 'description' id= 'description'> </textarea> <br>
    
    <button type='submit' value='Submit'>Create Property</button>";
            ?>
        </form>
    </div>

    <div class=recoveredProperties>
        <h1> Recover Deleted Property</h1>

        <?php
        try {
            $recoveryQuery = "SELECT propertyID, address1, address2, postcode, rent, bills, capacity, description, landlordID
                 FROM PropertyRecovery
                 ORDER BY propertyID";

            $recoveryResult = $dbConn->query($recoveryQuery);

            while ($rowObj = $recoveryResult->fetchObject()) {
                echo "<div class='recoveredPropertyList'>
				   <span class='PropertyID' style='display:none'>{$rowObj->propertyID}</span>
				   <span class='landlordID'>Landlord ID: {$rowObj->landlordID}</span>
				   <span class='address1'>Address 1: {$rowObj->address1}, {$rowObj->address2} {$rowObj->postcode}</span>
				   <span class='rent'>Rent: £{$rowObj->rent}</span>
				   <span class='bills'>Bills: £{$rowObj->bills}</span>
				   <span class='capacity'>Tenant Capacity: {$rowObj->capacity}</span>
				   <span class='description' style='display:none'>{$rowObj->description}</span><br> 
				   <span class='recoverProperty'><a href='PropertyRecover.php?propertyID={$rowObj->propertyID}'>Recover Property</a></span>
				   </div>\n
				   ";
            }
        } catch (Exception $e) {
            echo "<p>Query failed: " . $e->getMessage() . "</p>\n";
        }
        } else {
            echo "<p>You do not have permission to access this page.</p>";
        }
    } else {
        echo "<p>You do not have permission to access this page.</p>";
    }
        echo "</div>";

        echo "<div class = 'GapForFooter'> </div>";

        ?>
    </div>
</body>
</html>