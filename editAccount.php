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
    <title>Edit Account</title>
</head>

<body>

<?php
    require_once("functions.php");
    checkLogin('index');
    buildNav();
    buildFooter();
    turnButtGreen('editAcc');
?>

<script language="javascript" type="text/javascript">
function windowClose() {
    window.open('','_parent','');
    window.close();
}
</script>

<div class = "editAccount" style="display: block;">
    
    <?php
    $isAdmin = false;

    require_once("functions.php");
    $dbConn = getConnection();

    $querySQL1 = "SELECT username FROM Account INNER JOIN Admin ON Account.accountID = Admin.accountID ORDER BY username ASC";
    $queryResult1 = $dbConn->query($querySQL1);

    $username = $_SESSION['username'];

    while($rowObj1 = $queryResult1->fetchObject())
    {
        if($username == $rowObj1->username)
        {
            echo"<style>ul{display:none;}</style>";
            $isAdmin = true;

        }
    }

    $accountID = filter_has_var(INPUT_GET, 'accountID') ? $_GET['accountID'] : null;
    $accountID = trim($accountID);

    if (empty($accountID)) {
        
        require_once("functions.php");
        $dbConn = getConnection();

        $querySQL = "SELECT accountID FROM Account WHERE username = '$username'";
        $queryResult = $dbConn->prepare($querySQL);
        $queryResult -> execute(array(':username'=>$username));
        $user = $queryResult->fetchObject();

        $accountID = $user->accountID;
        $accountID = trim($accountID);

        try{
            require_once("functions.php");
            $dbConn = getConnection();

            $querySQL = "SELECT * FROM Account WHERE accountID = '$accountID'";
            $queryResult = $dbConn->prepare($querySQL);
            $queryResult -> execute(array(':accountID'=>$accountID));
            $rowObj = $queryResult->fetchObject();

            echo "        
            <div class='editAccountForm' id='editForm'>
            <form method='post' action='editAccountProcess.php' class='edit-container'>

            <h3>Account Details</h3>

            <table>
                <tr>
                    <th>Username</th>
                    <th><input type='text' value='{$rowObj->username}' name='username'</th>
                </tr>
                <tr>
                    <th>Password</th>
                    <th><input type='password' placeholder='Change Password?' name='passwordHash'</th>
                </tr>
                <tr>
                    <th>Email</th>
                    <th><input type='text' value='{$rowObj->email}' name='email'</th>
                </tr>
                <tr>
                    <th>First Name</th>
                    <th><input type='text' value='{$rowObj->firstName}' name='firstName'</th>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <th><input type='text' value='{$rowObj->lastName}' name='lastName'</th>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <th><input type='text' value='{$rowObj->phoneNumber}' name='phoneNumber'</th>
                </tr>
                <tr>
                    <th>Age</th>
                    <th><input type='text' value='{$rowObj->age}' name='age'</th>
                </tr>
                <tr>
                    <th>Gender</th>
                    <th><input type='text' value='{$rowObj->gender}' name='gender'</th>
                </tr>
                <tr>
                    <th>Account ID</th>
                    <th><input type='text' value='{$rowObj->accountID}' READONLY name='accountID'</th>
                </tr>
                <tr>
                    <th>Creation Date</th>
                    <th><input type='text' value='{$rowObj->creationDate}' READONLY name='creationDate'</th>
                </tr>
            </table>

            <button type='submit' class='btn' value='save'>Save Changes</button>
            <br>
            ";

            if($isAdmin === true){
                echo"<button type='button' class='btn cancel' onclick='window.close()'>Close</button>";
            }

            echo"
            </form>
            </div>
            ";

        } catch (Exception $e) {
            echo "<p>Account details not found: " . $e->getMessage() . "</p>\n";
        }
    }
    else 
    {
        try {
            require_once("functions.php");
            $dbConn = getConnection();

            $querySQL = "SELECT * FROM Account WHERE accountID = '$accountID'";
            $queryResult = $dbConn->prepare($querySQL);
            $queryResult -> execute(array(':accountID'=>$accountID));
            $rowObj = $queryResult->fetchObject();

            echo "        
            <div class='editAccountForm' id='editForm'>
            <form method='post' action='editAccountProcess.php' class='edit-container'>

            <h3>Account Details</h3>

            <table>
                <tr>
                    <th>Username</th>
                    <th><input type='text' value='{$rowObj->username}' name='username'</th>
                </tr>
                <tr>
                    <th>Password</th>
                    <th><input type='password' placeholder='Change Password?' name='passwordHash'</th>
                </tr>
                <tr>
                    <th>Email</th>
                    <th><input type='text' value='{$rowObj->email}' name='email'</th>
                </tr>
                <tr>
                    <th>First Name</th>
                    <th><input type='text' value='{$rowObj->firstName}' name='firstName'</th>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <th><input type='text' value='{$rowObj->lastName}' name='lastName'</th>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <th><input type='text' value='{$rowObj->phoneNumber}' name='phoneNumber'</th>
                </tr>
                <tr>
                    <th>Age</th>
                    <th><input type='text' value='{$rowObj->age}' name='age'</th>
                </tr>
                <tr>
                    <th>Gender</th>
                    <th><input type='text' value='{$rowObj->gender}' name='gender'</th>
                </tr>
                <tr>
                    <th>Account ID</th>
                    <th><input type='text' value='{$rowObj->accountID}' READONLY name='accountID'</th>
                </tr>
                <tr>
                    <th>Creation Date</th>
                    <th><input type='text' value='{$rowObj->creationDate}' READONLY name='creationDate'</th>
                </tr>
            </table>

            <button type='submit' class='btn' value='save'>Save Changes</button>
            <br>
            <button type='button' class='btn cancel' onclick='window.close()'>Close</button>
        
            </form>
            </div>
            ";

        } catch (Exception $e) {
            echo "<p>Account details not found: " . $e->getMessage() . "</p>\n";
        }
    }
    ?>

</div>
</body>