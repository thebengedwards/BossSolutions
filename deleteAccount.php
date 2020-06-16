<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <link rel=meta charset='UTF-8' />
    <link rel='stylesheet' type='text/css' href='stylesheet.css'>
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
    <title>Delete an account</title>
</head>

<body>

<?php
    require_once("functions.php");
    checkLogin('index');
    buildFooter();
?>

<div class = "deleteAccount" style="display: block;">
    <h3>Delete an Account</h3>
    <p>Remember to be carful when deleting an account</p>
    <hr>
    <script>
    function windowClose() {
        window.open('','_parent','');
        window.close();
    }
    function openViewDeleteFinal() {
        document.getElementById("viewDeleteForm").style.display = "block";
    }
    </script>
    
    <?php
    $accountID = filter_has_var(INPUT_GET, 'accountID') ? $_GET['accountID'] : null;
    $accountID = trim($accountID);
    $date = date('Y-m-d');
    $currentDate = date('y-m-d', strtotime($date));
    $seconds_in_year = 31536000;

    require_once("functions.php");
    $dbConn = getConnection();

    $querySQL = "SELECT accountID, username, firstName, lastName, email, creationDate FROM Account WHERE accountID = '$accountID'";
    $queryResult = $dbConn->query($querySQL);

    while($rowObj = $queryResult->fetchObject())
    {
        $creationDate = date('y-m-d', strtotime($rowObj->creationDate));
        $seconds_in_year = 31536000;
        $validDate = date('y-m-d', strtotime($rowObj->creationDate) + $seconds_in_year);

        echo"
        <p><b>An Account can only be deleted if it is a year old</b></p>
        <p>This account was created on:</p>
        <p>$creationDate</p>
        <p>It can be deleted after:</p>
        <p>$validDate</p>
        <hr>
        ";
        if($date<$validDate)
        {
            echo"
            <div class = 'false'>
                <h3>Account Cannot be Deleted yet</h3>
                <p>Please return to the account Management page</p>
                <hr>
                <button type='button' class='btn cancel' onclick='window.close()'>Close</button>
            </div>
            ";
        }
        else if($date>=$validDate)
        {
            echo"
            <div class = 'true'>
                <h3>Account is eligible to be Deleted</h3>
                <p><b>This process is irreversible. Are you sure you want ot delete this account?<b><p>
                <button class='btn' onclick='openViewDeleteFinal()'>Yes i am sure</button>
                
                <div class='deleteFinal' id='viewDeleteForm'>
                    <form method='post' action='deleteAccountProcess.php'>
                    <p>Deleting user: <input type='text' READONLY value='{$rowObj->username}' name='username'</p>
                    <p>Account ID: <input type='text' READONLY value='{$rowObj->accountID}' name='accountID'</p>
                    <p>First Name: <input type='text' READONLY value='{$rowObj->firstName}'</p>
                    <p>Last Name: <input type='text' READONLY value='{$rowObj->lastName}'</p>
                    <p><button type='submit' class='btn delete' value='save'>DELETE USER</button></p>
                    </form>
                </div>
                <hr>
                <button type='button' class='btn cancel' onclick='window.close()'>Close</button>
            </div>
            ";
        }
        else
        {
            echo"
            <div class = 'false'>
                Error, please sign out and try again later
                <button type='button' class='btn cancel' onclick='window.close()'>Close</button>
            <div>
            ";
        }
    }

    ?>

</div>
</body>