<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();

require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Assign Tenantsy</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>

<?php
require_once("functions.php");
$dbConn = getConnection();
$session = $_SESSION['username'];

$querySQL = "SELECT Account.accountID, landlordID
                 FROM Account
                 JOIN Landlord
                 ON Landlord.accountID = Account.accountID
                 WHERE username = '$session'";
$stmt = $dbConn->query($querySQL);
$stmt->execute(array(':username' => $session));
$user = $stmt->fetchObject();


@$accountID = $user->accountID;
@$landlordID = $user->landlordID;

$propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;
$tenantID = filter_has_var(INPUT_GET, 'username') ? $_GET['username'] : null;
$capacity = filter_has_var(INPUT_GET, 'capacity') ? $_GET['capacity'] : null;

$propertyID = trim($propertyID);
$tenantID = trim($tenantID);
$capacity = trim($capacity);


$errors = false;
if(empty($propertyID)) {
    $errors = true;
    echo"No Property has been specified <br>";
}

if(empty($tenantID)) {
    $errors = true;
    echo"No Tenant has been specified <br>";
}


if($errors == false) {
    if ($landlordID) {
        try {
            $dbConn = getConnection();
            $updateSQL = "UPDATE Tenant SET propertyID='$propertyID' WHERE ID='$tenantID'";
            $dbConn->exec($updateSQL);
            echo "<p>Property Successfully Updated </p>\n";
            echo "<p>Manage Tenants <a href='PropertyTenantManagement.php?propertyID=$propertyID'> here </a></span>";
        } catch (Exception $e) {
            echo "<p>Property not updated:</p>\n";
            echo "<p>Go to Property List by clicking <a href='PropertyLandlord.php'> here </a>.</p>\n";
        }
    } else {
        try {
            $dbConn = getConnection();
            $updateSQL = "UPDATE Tenant SET propertyID='$propertyID' WHERE ID='$tenantID'";
            $dbConn->exec($updateSQL);
            echo "<p>Property Successfully Updated </p>\n";
            echo "<p>Manage Tenants <a href='PropertyTenantManagement.php?propertyID=$propertyID'> here </a></span>";
        } catch (Exception $e) {
            echo "<p>Property not updated:</p>\n";
            echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'> here </a>.</p>\n";
        }

    }
}
?>

</body>
</html>
