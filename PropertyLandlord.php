<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Landlord Property Management</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico'/>
</head>
<body>

<?php
require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();
turnButtGreen('propertyMan');
?>

<h1> Property Management </h1>

<h2>My Properties</h2>

<?php
require_once("functions.php");
$dbConn = getConnection();
@$session = $_SESSION['username'];

$querySQL = "SELECT Account.accountID, Landlord.landlordID
                 FROM Account 
                 INNER JOIN Landlord
                 ON Account.accountID = Landlord.accountID
                 WHERE username = '$session'";
$stmt = $dbConn->query($querySQL);
$stmt->execute(array(':username' => $session));
$user = $stmt->fetchObject();

if ($_SESSION['loggedIn'] == true) {
    if (checkPermission('Landlord') == true) {
        try {
            $accountID = $user->accountID;
            $landlordID = $user->landlordID;
            //echo"$landlordID";
            //echo"$accountID";

            $sqlQuery = "SELECT propertyID, address1, address2, postcode, rent, bills, capacity, description, Landlord.landlordID, Property.landlordID, Landlord.accountID
                 FROM Property
                 JOIN Landlord
                 ON Property.landlordID = Landlord.landlordID
                 WHERE Property.landlordID = $landlordID";
            $queryResult = $dbConn->query($sqlQuery);
            echo "<div class = 'myProperties'>";
            while ($rowObj = $queryResult->fetchObject()) {
                echo "<div class = 'propertyList'>
                   <span class='PropertyID' style='display:none'>{$rowObj->propertyID}</span>
				   <span class='address'>Address: {$rowObj->address1}, {$rowObj->address2} </span><br>
				   <span class='postcode'>Postcode: {$rowObj->postcode}</span><br>
				   <span class='rent'>Rent: £{$rowObj->rent}</span><br>
				   <span class='bills'>Bills: £{$rowObj->bills}</span><br>
				   <span class='capacity'>Capacity: {$rowObj->capacity}</span><br><br>
				   <span class='description' style='display:none'>{$rowObj->description}</span>
			       <span class='manageProperty'><a href='PropertyEdit.php?propertyID={$rowObj->propertyID}'>Edit Property</a></span>
				   <span class='assignProperty'><a href='PropertyTenantManagement.php?propertyID={$rowObj->propertyID}'>Manage Tenant</a></span>
				   <span class='deleteProperty'><a href='PropertyDelete.php?propertyID={$rowObj->propertyID}'>Delete Property</a></span>

</div>
				   ";
            }
        } catch (Exception $e) {
            echo "<p>Query failed: " . $e->getMessage() . "</p>\n";
        }
        echo "</div>";
        ?>

        <script>
            function AcceptFunction() {
                var txt;
                if (confirm("Are you sure you want to delete this property?")) {
                    txt = "You pressed OK!";
                } else {
                    txt = "You pressed Cancel!";
                }
                document.getElementById("demo").innerHTML = txt;
            }

        </script>

        <div class="createProperty">
            <h1>Create Property</h1>
            <form class="newPropertyDetails" action="PropertyInsert.php" method="post">
                <label>Address 1: </label>
                <input type="text" placeholder="Address 1" name="address1" id="address1" required>

                <label>Address 2: </label>
                <input type="text" placeholder="Address 2" name="address2" id="address2"><br>

                <label>Postcode: </label>
                <input type="text" placeholder="Postcode" name="postcode" id="postcode" required><br>

                <label>Rent (pp/pm) </label>
                <input type="text" placeholder="Rent" name="rent" id="rent" required><br>

                <label>Bills (pp/pm) </label>
                <input type="text" placeholder="Estimated Bills" name="bills" id="bills" required><br>

                <label>Maximum Tenants: </label>
                <input type='text' placeholder='Maximum Capacity' name='capacity' id='capacity' required><br>

                <label>Description of Property</label><br>
                <textarea rows="8" cols="60" name='description' id='description'> </textarea> <br>

                <button type="submit" value="Submit">Create Property</button>
            </form>
        </div>


        <?php
        try {
// retrieving the information from phpmyadmin that is required for php
            $sqlQuery = "SELECT propertyID, address1, address2, postcode, rent, bills, capacity, description, landlordID
                 FROM PropertyRecovery
                 WHERE landlordID = '$landlordID'
                 ORDER BY landlordID";

            $queryResult = $dbConn->query($sqlQuery);

            $queryCount = $queryResult->rowCount();
            echo "<div class = recoveredProperties> ";
            echo "<h1> Recover Properties</h1>";
            if ($queryCount) {
                while ($rowObj = $queryResult->fetchObject()) {
                    echo "<div class='recoveredPropertyList'>
				   <span class='PropertyID' style='display:none'>{$rowObj->propertyID}</span>
				   <span class='landlordID' style='display:none'>Landlord ID: {$rowObj->landlordID}</span>
				   <span class='address1'>Address: {$rowObj->address1}, {$rowObj->address2} {$rowObj->postcode}</span>
				   <span class='rent'>Rent: £{$rowObj->rent}</span>
				   <span class='bills'>Bills: £{$rowObj->bills}</span>
				   <span class='capacity'>Capacity: {$rowObj->capacity}</span>
				   <span class='description' style='display:none'>{$rowObj->description}</span>
				   <span class='recoverProperty'><a href='PropertyRecover.php?propertyID={$rowObj->propertyID}'>Recover Property</a></span><br>
				   </div>\n
				   ";
                }
            } else {
                echo "<br> No Properties Available for Recovery";
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


?>
</div>
</body>
</html>
