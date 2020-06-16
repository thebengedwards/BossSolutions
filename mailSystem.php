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
    <title>Email System</title>
</head>
<body>

    <?php

        require_once('functions.php');
        checkLogin('index');
    
        buildFooter();

        if ($_SESSION['loggedIn'] == true)
        {
        if (checkPermission('Admin') == true)
        {

    ?>

    <script>
        function openAllForm() {
            document.getElementById("allForm").style.display = "block";
            document.getElementById("specForm").style.display = "none";
            document.getElementById("propForm").style.display = "none";
            document.getElementById("indivForm").style.display = "none";
        }
        function closeAllForm() {
            document.getElementById("allForm").style.display = "none";
        }
        function openSpecForm() {
            document.getElementById("specForm").style.display = "block";
            document.getElementById("allForm").style.display = "none";
            document.getElementById("propForm").style.display = "none";
            document.getElementById("indivForm").style.display = "none";
        }
        function closeSpecForm() {
            document.getElementById("specForm").style.display = "none";
        }
        function openPropForm() {
            document.getElementById("propForm").style.display = "block";
            document.getElementById("allForm").style.display = "none";
            document.getElementById("specForm").style.display = "none";
            document.getElementById("indivForm").style.display = "none";
        }
        function closePropForm() {
            document.getElementById("propForm").style.display = "none";
        }
        function openIndivForm() {
            document.getElementById("indivForm").style.display = "block";
            document.getElementById("allForm").style.display = "none";
            document.getElementById("specForm").style.display = "none";
            document.getElementById("propForm").style.display = "none";
        }
        function closeIndivForm() {
            document.getElementById("indivForm").style.display = "none";
        }
        function windowClose() {
        window.open('','_parent','');
        window.close();
        }  
    </script>

    <div class="emailSystem">
        <h3>Welcome to the Email System</h3>
        <p>Please select below what you would like to do:</p>
        <button type='button' class='btn cancel' onclick='window.close()'>Close</button>
        <br>

        <button class="Button" onclick="openAllForm()">Send To All Users</button>
        <button class="Button" onclick="openSpecForm()">Send To Select Type of Users</button>
        <button class="Button" onclick="openPropForm()">Send to Specific Property</button>
        <button class="Button" onclick="openIndivForm()">Send to Select Individuals</button>

        <hr>

        <div class="sendToAll" id="allForm">
            <form action="emailAllProcess.php" method="post">
                <p>Email is being sent out to all users</p>
                <p>What is the subject of the email:</p>
                <p><input type="text" value="A Message from Boss Solutions" name="subject" ></p>
                Message:<br>
                <textarea rows="8" name="message" cols="30">Hello
This email is being sent to all users on the BossSolutions™ Platform.



Thank you for reading,
Boss Solutions Team
                </textarea>
                <br>
                <button type="submit" class="sendMail" value="Submit">Send Mail</button>
            </form>
        </div>

        <div class="sendToSpec" id="specForm">
            <form action="emailSpecProcess.php" method="post">
                <p>Choose which type of account to email:</p>
                <select class="selectcss" id="accountType" name="accountType">
                    <option value="admin">Administrator</option>
                    <option value="landlord">Landlord</option>
                    <option value="tenant">Tenant</option>
                    <option value="guest">Guest</option>
                </select>
                <p>What is the subject of the email:</p>
                <p><input type="text" value="A Message from Boss Solutions" name="subject" ></p>
                Message:<br>
                <textarea rows="8" name="message" cols="30">Hello
This email is being sent to all users with a similar account type on the BossSolutions™ Platform.



Thank you for reading,
Boss Solutions Team
                </textarea>
                <br>
                <button type="submit" class="sendMail" value="Submit">Send Mail</button>
            </form>
        </div>

        <div class="sendToProp" id="propForm">
            <form action="emailPropertyProcess.php" method="post">
                <p>Choose a property to email:</p>
                <?php
                require_once("functions.php");
                $dbConn = getConnection();

                $querySQL = "SELECT propertyID, address1, address2, postcode FROM Property";
                $queryResult = $dbConn->query($querySQL);

                if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {

                        echo"
                        <select class='selectcss 'id='propertyID' name='propertyID'>
                            <option value='NULL'>---Choose A Property---</option>
                        ";
                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo"<option value=$rowObj->propertyID>$rowObj->address1, $rowObj->address2, $rowObj->postcode</option>";
                        }
                        echo"</select>";
                    }
                ?>
                <br>
                <p>What is the subject of the email:</p>
                <p><input type="text" value="A Message from Boss Solutions" name="subject" ></p>
                Message:<br>
                <textarea rows="8" name="message" cols="30">Hello
This email is being sent to all users at your Property.



Thank you for reading,
Boss Solutions Team
                </textarea>
                <br>
                <button type="submit" class="sendMail" value="Submit">Send Mail</button>
            </form>
        </div>

        <script>
        
        function openViewAdmins() {
            document.getElementById("viewAdminForm").style.display = "block";
            document.getElementById("viewLandlordForm").style.display = "none";
            document.getElementById("viewTenantForm").style.display = "none";
            document.getElementById("viewGuestForm").style.display = "none";
        }
        function closeViewAdmins() {
            document.getElementById("viewAdminForm").style.display = "none";
        }

        function openViewLandlords() {
            document.getElementById("viewLandlordForm").style.display = "block";
            document.getElementById("viewAdminForm").style.display = "none";
            document.getElementById("viewTenantForm").style.display = "none";
            document.getElementById("viewGuestForm").style.display = "none";
        }
        function closeViewLandlords() {
            document.getElementById("viewLandlordForm").style.display = "none";
        }
        function openViewTenants() {
            document.getElementById("viewTenantForm").style.display = "block";
            document.getElementById("viewAdminForm").style.display = "none"
            document.getElementById("viewLandlordForm").style.display = "none";
            document.getElementById("viewGuestForm").style.display = "none";
        }
        function closeViewTenants() {
            document.getElementById("viewTenantForm").style.display = "none";
        }
        function openViewGuests() {
            document.getElementById("viewGuestForm").style.display = "block";
            document.getElementById("viewAdminForm").style.display = "none";
            document.getElementById("viewLandlordForm").style.display = "none";
            document.getElementById("viewTenantForm").style.display = "none";
        }
        function closeViewGuests() {
            document.getElementById("viewGuestForm").style.display = "none";
        }
        </script>
        <div class="sendToIndiv" id="indivForm">
            <p>Select who you would like to email</p>
            <div class="selector"> 
                <p>Select who you would like to send the email to</p>
                <button class="Button" onclick="openViewAdmins()">Admins</button>
                <button class="Button" onclick="openViewLandlords()">Landlords</button>
                <button class="Button" onclick="openViewTenants()">Tenants</button>
                <button class="Button" onclick="openViewGuests()">Guests</button>
                <hr>
                <form action="emailPeopleProcess.php" method="post">
                <div class = "chooseFromAdmins" id="viewAdminForm">
                    <?php
                    require_once("functions.php");
                    $dbConn = getConnection();

                    $querySQL = "SELECT * FROM Account INNER JOIN Admin ON Account.accountID = Admin.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo"
                            <select class='selectcss 'id='adminUserID' name='adminUserID'>
                                <option value='NULL'>---Choose An Admin---</option>
                        ";
                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo"<option value=$rowObj->accountID>User: $rowObj->username, Email: $rowObj->email</option>";
                        }
                    echo"</select>";
                    }
                    ?>
                </div>
                <div class = "chooseFromLandlords" id="viewLandlordForm">
                    <?php
                    require_once("functions.php");
                    $dbConn = getConnection();

                    $querySQL = "SELECT * FROM Account INNER JOIN Landlord ON Account.accountID = Landlord.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo"
                            <select class='selectcss' id='landlordUserID' name='landlordUserID'>
                                <option value='NULL'>---Choose A Landlord---</option>
                        ";
                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo"<option value=$rowObj->accountID>User: $rowObj->username, Email: $rowObj->email</option>";
                        }
                    echo"</select>";
                    }
                    ?>
                </div>
                <div class = "chooseFromTenants" id="viewTenantForm">
                    <?php
                    require_once("functions.php");
                    $dbConn = getConnection();

                    $querySQL = "SELECT * FROM Account INNER JOIN Tenant ON Account.accountID = Tenant.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo"
                            <select class='selectcss' id='tenantUserID' name='tenantUserID'>
                                <option value='NULL'>---Choose A Tenant---</option>
                        ";
                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo"<option value=$rowObj->accountID>User: $rowObj->username, Email: $rowObj->email</option>";
                        }
                    echo"</select>";
                    }
                    ?>
                </div>
                <div class = "chooseFromGuests" id="viewGuestForm">
                    <?php
                    require_once("functions.php");
                    $dbConn = getConnection();

                    $querySQL = "SELECT * FROM Account INNER JOIN Guest ON Account.accountID = Guest.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo"
                            <select class='selectcss' id='guestUserID' name='guestUserID'>
                                <option value='NULL'>---Choose A Guest---</option>
                        ";
                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo"<option value=$rowObj->accountID>User: $rowObj->username, Email: $rowObj->email</option>";
                        }
                    echo"</select>";
                    }
                    ?>
                </div>

                </div>
                <div class="message">
                    <p>Type the email here</p>
                    <p>What is the subject of the email:</p>
                    <p><input type="text" value="A Message from Boss Solutions" name="subject" ></p>
                    Message:<br>
                    <textarea rows="8" name="message" cols="30">Hello
This email is being sent to just you.



Thank you for reading,
Boss Solutions Team
                </textarea>
                    <br>
                    <button type="submit" class="sendMail" value="Submit">Send Mail</button>
                </div>
                </form>
        </div>
    </div>

    <?php
    }else{
        echo "<p style='text-align: center'>You do not have permission to access this page.</p>";
    }
    }else{
        echo "<p style='text-align: center'>You do not have permission to access this page.</p>";
    }
    ?>
</body>
</html>