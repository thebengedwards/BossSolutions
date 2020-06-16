<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Insert Property</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>
<?php
require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();

$landlord = filter_has_var(INPUT_POST, 'username') ? $_POST['username'] : null;
$address1 = filter_has_var(INPUT_POST, 'address1') ? $_POST['address1'] : null;
$address2 = filter_has_var(INPUT_POST, 'address2') ? $_POST['address2'] : null;
$postcode = filter_has_var(INPUT_POST, 'postcode') ? $_POST['postcode'] : null;
$rent = filter_has_var(INPUT_POST, 'rent') ? $_POST['rent'] : null;
$bills = filter_has_var(INPUT_POST, 'bills') ? $_POST['bills'] : null;
$capacity = filter_has_var(INPUT_POST, 'capacity') ? $_POST['capacity'] : null;
$description = filter_has_var(INPUT_POST, 'description') ? $_POST['description'] : null;

$landlord = trim($landlord);
$address1 = trim($address1);
$address2 = trim($address2);
$postcode = trim($postcode);
$rent = trim($rent);
$bills = trim($bills);
$capacity = trim($capacity);

$errors = false;

//Basic Validation and sanitisation for each attribute
if (empty($address1)) {
    echo "<p>Enter the properties 1st Address</p>\n";
    $errors = true;
} else if (strlen($address1) > 40) {
    echo "<p>The address must be no more than 40 characters</p>\n";
    $errors = true;
}

if (strlen($address2) > 40) {
    echo "<p>The address must be no more than 40 characters</p>\n";
    $errors = true;
}

if (empty($postcode)) {
    echo "<p>Enter the postcode</p>\n";
    $errors = true;
} else if (strlen($postcode) > 60) {
    echo "<p>The property must be no more than 10 characters long</p>\n";
    $errors = true;
}
if (empty($rent)) {
    echo "<p>You need to input a price for the RENT.</p>\n";
    $errors = true;
} else if (strlen($rent) > 10) {
    echo "<p>The rent price must be no more than 10 characters</p>\n";
    $errors = true;
} else if (!filter_var($rent, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The price should be a number</p>\n";
    $errors = true;
}
if (empty($bills)) {
    echo "<p>You need to input a price for the BILLS.</p>\n";
    $errors = true;
} else if (strlen($bills) > 10) {
    echo "<p>The bills price must be no more than 10 characters</p>\n";
    $errors = true;
} else if (!filter_var($bills, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The price should be a number</p>\n";
    $errors = true;
}

if (empty($capacity)) {
    echo "<p>You need to input a maximum capacity of tenants.</p>\n";
    $errors = true;
} else if (strlen($capacity) > 2) {
    echo "<p>The capacity of the property must be 2 digits or less</p>\n";
    $errors = true;
} else if (!filter_var($capacity, FILTER_VALIDATE_FLOAT)) {
    echo "<p>The capacity should be a number</p>\n";
    $errors = true;
}


if (strlen($description) > 300) {
    echo "<p>The description of the property cannot exceed 300 letters.</p>\n";
    $errors = true;
}
// if sanitization and validation fails then redirect to an error page with a link back the the property list
if ($errors === true) {
    echo "<p>Please try <a href='PropertyInsert.php'>again</a>.</p>\n";
} else if ($landlord) {
try {
    header('Location:PropertyAdmin.php');
    require_once("functions.php");
    $dbConn = getConnection();

        $SQLinsert = "INSERT INTO Property(address1, address2, postcode, rent, bills, capacity, description, landlordID)
              VALUES (:address1, :address2, :postcode, :rent, :bills, :capacity, :description, '$landlord')";
        $stmt = $dbConn->prepare($SQLinsert);
        $stmt->execute(array(':address1' => $address1, ':address2' => $address2, ':postcode' => $postcode, ':rent' => $rent, ':bills' => $bills, ':capacity' => $capacity, ':description' => $description));
        echo "<p>Property Successfully Inserted </p>\n";
        echo "<p>Go back to your property list <a href='PropertyAdmin.php'>here</a>.</p>\n";
} catch (Exception $e) {
    echo "<p>Property Insertion unsuccesful: " . $e->getMessage() . "</p>\n";
    echo "<p><a href='PropertyAdmin.php'>Try Again</a>.</p>\n";
}
    } else {
    try {
        header('Location:PropertyLandlord.php');
        $session = $_SESSION['username'];
        $dbConn = getConnection();
        $querySQL = "SELECT Account.accountID, landlordID
                 FROM Account
                 JOIN Landlord
                 ON Landlord.accountID = Account.accountID
                 WHERE username = '$session'";
        $stmt = $dbConn->query($querySQL);
        $stmt->execute(array(':username' => $session));
        $user = $stmt->fetchObject();

        $accountID = $user->accountID;
        $landlordID = $user->landlordID;


        $SQLinsert2 = "INSERT INTO Property(address1, address2, postcode, rent, bills, capacity, description, landlordID)
              VALUES (:address1, :address2, :postcode, :rent, :bills, :capacity, :description,  '$landlordID')";
        $stmt = $dbConn->prepare($SQLinsert2);
        $stmt->execute(array(':address1' => $address1, ':address2' => $address2, ':postcode' => $postcode, ':rent' => $rent, ':bills' => $bills, ':capacity' => $capacity, ':description' => $description));
        echo "<p>Property Successfully Inserted </p>\n";
        echo "<p>Go back to your property list <a href='PropertyLandlord.php'>here</a>.</p>\n";
    } catch (Exception $e) {
        echo "<p>Property Insertion unsuccesful: " . $e->getMessage() . "</p>\n";
        echo "<p><a href='PropertyLandlord.php'>Try Again</a>.</p>\n";
    }
}


?>
</body>
</html>
