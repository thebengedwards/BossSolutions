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
    <title>Update Property</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>

<?php
// declaration of attributes
$propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;
$address1 = filter_has_var(INPUT_GET, 'address1') ? $_GET['address1'] : null;
$address2 = filter_has_var(INPUT_GET, 'address2') ? $_GET['address2'] : null;
$postcode = filter_has_var(INPUT_GET, 'postcode') ? $_GET['postcode'] : null;
$rent = filter_has_var(INPUT_GET, 'rent') ? $_GET['rent'] : null;
$bills = filter_has_var(INPUT_GET, 'bills') ? $_GET['bills'] : null;
$capacity = filter_has_var(INPUT_GET, 'capacity') ? $_GET['capacity'] : null;
$description = filter_has_var(INPUT_GET, 'description') ? $_GET['description'] : null;

$propertyID = trim($propertyID);
$address1 = trim($address1);
$address2 = trim($address2);
$postcode = trim($postcode);
$rent = trim($rent);
$bills = trim($bills);
$capacity = trim($capacity);

$errors = false;

//Basic Validation and sanitisation for each attribute
if (empty($propertyID)) {
    echo "<p>You need to have selected a property.</p>\n";
    $errors = true;
} else if (strlen($propertyID) > 40) {
    echo "<p>The propertyID must be no more than 10 characters</p>\n";
    $errors = true;
}

if (empty($address1)) {
    echo "<p>Enter the properties address 1</p>\n";
    $errors = true;
} else if (strlen($address1) > 40) {
    echo "<p>The address must be no more than 40 characters</p>\n";
    $errors = true;
}

if (empty($address2)) {
    echo "<p>Enter the properties address 2</p>\n";
    $errors = true;
} else if (strlen($address2) > 40) {
    echo "<p>The address must be no more than 40 characters</p>\n";
    $errors = true;
}

if (empty($postcode)) {
    echo "<p>Enter the postcode</p>\n";
    $errors = true;
} else if (strlen($postcode) > 10) {
    echo "<p>The postcode must be no more than 10 characters long</p>\n";
    $errors = true;
}
if (empty($rent)) {
    echo "<p>You need to input a price for the RENT.</p>\n";
    $errors = true;
} else if (strlen($rent) > 5) {
    echo "<p>The rent price must be no more than 5 characters</p>\n";
    $errors = true;
} else if (!filter_var($rent, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The price should be a number</p>\n";
    $errors = true;
}
if (empty($bills)) {
    echo "<p>You need to input a price for the BILLS.</p>\n";
    $errors = true;
} else if (strlen($bills) > 5) {
    echo "<p>The bills price must be no more than 5 characters</p>\n";
    $errors = true;
} else if (!filter_var($bills, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The price should be a number</p>\n";
    $errors = true;
}

if (empty($capacity)) {
    echo "<p>You need to input a maximum capacity.</p>\n";
    $errors = true;
} else if (strlen($capacity) > 10) {
    echo "<p>The maximum capacity must be no more than 10</p>\n";
    $errors = true;
} else if (!filter_var($capacity, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The capacity should be a number</p>\n";
    $errors = true;
}


if (empty($description)) {
    echo "<p>You need to input a description.</p>\n";
    $errors = true;
} else if (strlen($description) >= 300) {
    echo "<p>No more than 300 characters</p>\n";
    $errors = true;
}

// if sanitisation and validation fails then redirect to an error page with a link back the the property list
if ($errors === true) {
    echo "<p>Please try <a href='PropertyAdmin.php'>again</a>.</p>\n";
} else {
    require_once("functions.php");
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
    @$landlordID = $user->landlordID;

    try {
        if ($landlordID) {
            $updateSQL = "UPDATE Property SET propertyID='$propertyID', address1='$address1', address2='$address2', postcode='$postcode', rent='$rent', bills='$bills', capacity='$capacity', description='$description'  WHERE propertyID = '$propertyID'";
            $dbConn->exec($updateSQL);
            echo "<p>Property Successfully Updated </p>\n";
            echo "<p><a href='PropertyLandlord.php'>Update another property here</a>.</p>\n";
        } else {
            $updateSQL = "UPDATE Property SET propertyID='$propertyID', address1='$address1', address2='$address2', postcode='$postcode', rent='$rent', bills='$bills', capacity='$capacity', description='$description'  WHERE propertyID = '$propertyID'";
            $dbConn->exec($updateSQL);
            echo "<p>Property Successfully Updated </p>\n";
            echo "<p><a href='PropertyAdmin.php'>Update another property</a>.</p>\n";
        }
    } catch (Exception $e) {
//error message if the submit fails
        echo "<p>Property not updated correctly: " . $e->getMessage() . "</p>\n";
        echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
    }
}
?>

</body>
</html>
