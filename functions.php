<?php
/**
 * Created by PhpStorm.
 * User: calumwillis
 * Date: 13/02/2020
 * Time: 17:00
 */
function getConnection() { //Connects to the database, throws an exemption if there is a connection issue.
    try {
        $connection = new PDO("mysql:host=localhost;dbname=unn_w17004394",
            "unn_w17004394", "PASSWORDHERE");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (Exception $e) {
        throw new Exception("Connection error ". $e->getMessage(), 0, $e);
    }
}

function set_session($key, $value) //Sets the session.
{
    $_SESSION[$key] = $value;
    return true;
}

function log_error($e) //Logs errors to a file.
{
    $fileHandle = fopen("error_log_file.log", "ab");
    $errorDate = date('D M j G:i:s T Y');
    $errorMessage = $e->getMessage();
    fwrite($fileHandle, "$errorDate, $errorMessage /n");
    fclose($fileHandle);
}

function checkLogin($redirect) //Checks in the user is logged in or not. Call this at the top of each page.
{
    if (!isset($_SESSION['loggedIn']))
    {
        $_SESSION['loggedIn'] = false;
    }
    
    if (!isset($_SESSION['userID']))
    {
        $_SESSION['userID'] = null;
    }
    
    $_SESSION['redirect'] = $redirect;
    if ($_SESSION['loggedIn'] == false)
    {
        buildLoggedOut();
    }
    else
    {
        buildLoggedIN();
    }

}



function buildLoggedOut() //Builds the form to log in, when the user is logged out.
{
    echo
    "
        <div class='lOtop'>
            <a href='index.php'><img id='logo' src='Logo.png'></a>
            <div class='formHolder'>
                <form id='loginForm' action='loginProcess.php' method='post' > 
                            <span>Username: <input class='formInput' type='text' name='username'></span>
                            <span>Password: <input class='formInput' type='password' name='password' ></span>
                            <input type='submit' name='submit' value='Go'>
                </form>
            </div>
        </div>
    ";
}

function buildLoggedIN() //Builds the form to log out, when the user is logged in.
{
    echo
    "
        <div class='lOtop'>
            <a href='index.php'><img id='logo' src='Logo.png'></a>
            <div class='formHolder'>
                <form id='logOutForm' action='logOutProcess.php' method='post' > 
                            <span>You are currently logged in as </span>";
                                echo $_SESSION['username']; echo ".   ";
                            echo "<input class='logOutButt' type='submit' name='submit' value='Logout'>
                </form>
            </div>
        </div>
    ";
}

function checkPermission($accountType) //Enter what type of user you are looking to check as the parameter. Either "Admin", "Landlord", "Guest" or "Tenant".
{
    $dbConn = getConnection();

    $querySQL = "SELECT accountID
                 FROM ".$accountType." 
                 WHERE accountID = :id";

    $stmt = $dbConn->prepare($querySQL);
    $stmt->execute(array(':id' => $_SESSION['userID']));

    $user = $stmt->fetchObject();

    if ($user)
    {
        
        if ($_SESSION['loggedIn'] == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function buildPage()
{
    echo"<link rel=meta charset='UTF-8' />";
    echo"<link rel='stylesheet' type='text/css' href='stylesheet.css' />"; 
    echo"<link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />";
}

function buildNav()
{
    if ($_SESSION['loggedIn'] == true)
    {
        $checkTenant = checkPermission('Tenant');
        if ($checkTenant == true) {
            buildTenantNav();
        }

        $checkAdmin = checkPermission('Admin');
        if ($checkAdmin == true) {
            buildAdminNav();

        }

        $checkLandlord = checkPermission('Landlord');
        if ($checkLandlord == true) {
            buildLandlordNav();
        }

        $checkGuest = checkPermission('Guest');
        if ($checkGuest == true) {
            buildGuestNav();
        }
    }
    else
    {
        buildLoggedOutNav();
    }
    
    
}

// BUILDS ADMIN NAVIGATION BAR
function buildAdminNav()
{
     echo 
        "
            <ul>
                <li class='navButt' id='index'><a href='index.php'>Home</a></li>
                <li class='navButt' id='propertyMan'><a href='PropertyAdmin.php'>Property Management</a></li>
                <li class='navButt' id='adminBills'><a href='adminBills.php'>Transaction Management</a></li>
                <li class='navButt' id='adminAccMan'><a href='adminAccMan.php'>Account Management</a></li>
                <li class='navButt' id='forum'><a href='forum.php'>Forum</a></li>
                <li class='navButt' id='manageForum'><a href='manageForum.php'>Manage Forums</a></li>
                <li class='navButt' id='tenantMessage'><a href='#'>Tenant Communication</a></li>
                
            </ul>
            
        ";
    
}

// BUILDS LANDLORD NAVIGATION BAR
function buildLandlordNav()
{
     echo 
        "
            <ul>
                <li class='navButt' id='index'><a href='index.php'>Home</a></li>
                <li class='navButt' id='propertyMan'><a href='PropertyLandlord.php'>Property Management</a></li>
                <li class='navButt' id='landlordBills'><a href='landlordBills.php'>Property Income</a></li>
                <li class='navButt' id='editAcc'><a href='editAccount.php'>My Account</a></li>
                <li class='navButt' id='tenantMessage'><a href='#'>Tenant Communication</a></li>
            </ul>
            
        ";
    
}

// BUILDS TENANT NAVIGATION BAR
function buildTenantNav()
{
    $tempDate = date('Y');
    echo
        "
            <ul class='test'>
                <li class='navButt' id='index'><a href='index.php'>Home</a></li>
                <li class='navButt' id='propertyMan'><a href='PropertyTenant.php'>My Property</a></li>
                <li class='navButt' id='tenantBills'><a href='tenantBills.php?{$tempDate}'>My Bills</a></li>
                <li class='navButt' id='editAcc'><a href='editAccount.php'>My Account</a></li>
                <li class='navButt' id='forum'><a href='forum.php'>Forum</a></li>
                <li class='navButt' id='tenantMessage'><a href='#'>Report Problem</a></li>
            </ul>
            
        ";
    
}


// BUILDS GUEST NAVIGATION BAR
function buildGuestNav()
{
     echo 
        "
            <ul>
                <li class='navButt' id='index'><a href='index.php'>Home</a></li>
                <li class='navButt' id='editAcc'><a href='editAccount.php'>My Account</a></li>
                <li class='navButt' id='forum'><a href='forum.php'>Forum</a></li>
            </ul>
            
        ";
}

function buildLoggedOutNav()
{
    echo
    "
            <ul>
                <li class='navButt' id='index'><a href='index.php'>Home</a></li>
                <li class='createGuestButt' id='createGuest'><a href='indexGuest.php'>Create Guest Account</a></li>
            </ul>
            
        ";
}

function buildFooter()
{
    echo
    "
     <a href='index.php'>
        <div class='footer'>
          <p>Property Management System by Boss Solutions™</p>
        </div>
     </a>
    ";

}

function turnButtGreen($button)
{
    echo
    "
        <script type='text/javascript'>
            var gameButt = document.getElementById('$button');
            gameButt.style.backgroundColor = '#1ed760';
        </script>    
    ";
}

function chartJS()
{
    echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js'></script>";
}


?>


