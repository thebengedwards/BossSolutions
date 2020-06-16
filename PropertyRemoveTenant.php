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
        <title>Remove Tenant</title>
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
    $tenantID = filter_has_var(INPUT_GET, 'tenantID') ? $_GET['tenantID'] : null;

    if ($landlordID) {
        try {
            $SQLremove = "UPDATE Tenant
                  SET PropertyID = NULL
                  WHERE ID = $tenantID";
            $stmt = $dbConn->prepare($SQLremove);
            $stmt->execute();

            echo "<p>Tenant Successfully Removed </p>\n";
            echo "<p>Go to your Property List by clicking <a href='PropertyLandlord.php'>here</a>.</p>\n";
        } catch (Exception $e) {
            echo "<p>Tenant removal unsuccessful please try again" . $e->getMessage() . "</p>\n";
            echo "<p>Go to your Property List by clicking <a href='PropertyLandlord.php'>here</a>.</p>\n";
        }
    } else {
        try {
            $SQLremove = "UPDATE Tenant
                  SET PropertyID = NULL
                  WHERE ID = $tenantID";
            $stmt = $dbConn->prepare($SQLremove);
            $stmt->execute();

            echo "<p>Tenant Successfully Removed </p>\n";
            echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
        } catch (Exception $e) {
            echo "<p>Tenant removal unsuccessful please try again" . $e->getMessage() . "</p>\n";
            echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
        }

    }

?>

    </body>
</html>
