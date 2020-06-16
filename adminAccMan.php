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
    <title>Home Page</title>
</head>
<body>

    <?php

        require_once('functions.php');
        checkLogin('index');
    
        buildNav();
    
        buildFooter();

        turnButtGreen('adminAccMan');

        if ($_SESSION['loggedIn'] == true)
        {
        if (checkPermission('Admin') == true)
        {

    ?>
    
    <script>
        function openCreateAccount() {
            document.getElementById("createForm").style.display = "block";
            document.getElementById("viewAccounts").style.display = "none";
        }
        function closeCreateAccount() {
            document.getElementById("createForm").style.display = "none";
        }

        function openViewAll() {
            document.getElementById("viewAccounts").style.display = "block";
            document.getElementById("createForm").style.display = "none";
        }
        function closeViewAll() {
            document.getElementById("viewAccounts").style.display = "none";
        }
        function openEditAccount() {
            window.open("editAccount.php", "_blank")
        }
        function openEmailSystem() {
            window.open("mailSystem.php", "_blank")
        }
    </script>

    <div class="accManagment">
        <h3>Welcome to Account Managment</h3>
        <p>Please select below what you would like to do:</p>

        <button class="Button" onclick="openCreateAccount()">Create a new Account</button>
        <button class="Button" onclick="openViewAll()">View All Accounts</button>
        <button class="Button" onclick="openEditAccount()">Edit Your Account</button>
        <button class="Button" onclick="openEmailSystem()">Email System</button>

        <hr>

        <div class="createAccount" id="createForm">

            <script>

                function openCreateAdmin() {
                    document.getElementById("createAdminForm").style.display = "block";
                    document.getElementById("createLandlordForm").style.display = "none";
                    document.getElementById("createTenantForm").style.display = "none";
                    document.getElementById("createGuestForm").style.display = "none";
                }
                function closeCreateAdmin() {
                    document.getElementById("createAdminForm").style.display = "none";
                }

                function openCreateLandlord() {
                    document.getElementById("createLandlordForm").style.display = "block";
                    document.getElementById("createAdminForm").style.display = "none";
                    document.getElementById("createTenantForm").style.display = "none";
                    document.getElementById("createGuestForm").style.display = "none";
                }
                function closeCreateLandlord() {
                    document.getElementById("createLandlordForm").style.display = "none";
                }

                function openCreateTenant() {
                    document.getElementById("createTenantForm").style.display = "block";
                    document.getElementById("createAdminForm").style.display = "none";
                    document.getElementById("createLandlordForm").style.display = "none";
                    document.getElementById("createGuestForm").style.display = "none";
                }
                function closeCreateTenant() {
                    document.getElementById("createTenantForm").style.display = "none";
                }

                function openCreateGuest() {
                    document.getElementById("createGuestForm").style.display = "block";
                    document.getElementById("createAdminForm").style.display = "none";
                    document.getElementById("createLandlordForm").style.display = "none";
                    document.getElementById("createTenantForm").style.display = "none";
                }
                function closeCreateGuest() {
                    document.getElementById("createGuestForm").style.display = "none";
                }

            </script>

            <button class="Button" onclick="openCreateAdmin()">Create a new Admin</button>
            <button class="Button" onclick="openCreateLandlord()">Create a new Landlord</button>
            <button class="Button" onclick="openCreateTenant()">Create a new Tenant</button>
            <button class="Button" onclick="openCreateGuest()">Create a new Guest</button>

            <div class="createNewAdmin" id="createAdminForm">
                <form method="post" action="createAdminProcess.php" class="create-container">
                    <h1>Create New Admin</h1>

                    <label for="username"><b>Username:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <br>

                    <label for="email"><b>Email:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Email" name="email" required>
                    <br>

                    <label for="password"><b>Password:</b></label>
                    <br>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <br>

                    <hr>
                    
                    <button type="submit" class="btn" value="logon">Create Admin Account</button>
                    <button type="button" class="btn cancel" onclick="closeCreateAdmin()">Close</button>
                </form>
            </div>

            <div class="createNewLandlord" id="createLandlordForm">
                <form method="post" action="createLandlordProcess.php" class="create-container">
                    <h1>Create New Landlord</h1>

                    <label for="username"><b>Username:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <br>

                    <label for="firstName"><b>First Name:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Name" name="firstName" required>
                    <br>
                    
                    <label for="lastName"><b>Surname:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Surname" name="lastName" required>
                    <br>

                    <label for="email"><b>Email:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Email" name="email" required>
                    <br>

                    <label for="phoneNumber"><b>Contact Number:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Contact Number" name="phoneNumber" required>
                    <br>

                    <label for="age"><b>Your Age:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your current Age" name="age" required>
                    <br>

                    <label for="gender"><b>Gender:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Gender" name="gender" required>
                    <br>
                    
                    <label for="password"><b>Password:</b></label>
                    <br>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <br>

                    <hr>
                    
                    <button type="submit" class="btn" value="logon">Create Landlord Account</button>
                    <button type="button" class="btn cancel" onclick="closeCreateLandlord()">Close</button>
                </form>
            </div>

            <div class="createNewTenant" id="createTenantForm">
                <form method="post" action="createTenantProcess.php" class="create-container">
                    <h1>Create New Tenant</h1>

                    <label for="username"><b>Username:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <br>

                    <label for="firstName"><b>First Name:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Name" name="firstName" required>
                    <br>
                    
                    <label for="lastName"><b>Surname:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Surname" name="lastName" required>
                    <br>

                    <label for="email"><b>Email:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Email" name="email" required>
                    <br>

                    <label for="phoneNumber"><b>Contact Number:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Contact Number" name="phoneNumber" required>
                    <br>

                    <label for="age"><b>Your Age:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your current Age" name="age" required>
                    <br>

                    <label for="gender"><b>Gender:</b></label>
                    <br>
                    <input type="text" placeholder="Enter your Gender" name="gender" required>
                    <br>
                    
                    <label for="password"><b>Password:</b></label>
                    <br>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <br>

                    <hr>
                    
                    <button type="submit" class="btn" value="logon">Create Tenant Account</button>
                    <button type="button" class="btn cancel" onclick="closeCreateTenant()">Close</button>
                </form>
            </div>

            <div class="createNewGuest" id="createGuestForm">
                <form method="post" action="createGuestProcess.php" class="create-container">
                    <h1>Create New Guest</h1>

                    <label for="username"><b>Username:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <br>

                    <label for="email"><b>Email:</b></label>
                    <br>
                    <input type="text" placeholder="Enter Email" name="email" required>
                    <br>

                    <label for="password"><b>Password:</b></label>
                    <br>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <br>

                    <hr>
                    
                    <button type="submit" class="btn" value="logon">Create Guest Account</button>
                    <button type="button" class="btn cancel" onclick="closeCreateGuest()">Close</button>
                </form>
            </div>

        </div>

        <div class="allAccounts" id="viewAccounts">

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
                    document.getElementById("viewAdminForm").style.display = "none";
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

            <button class="Button" onclick="openViewAdmins()">View all Admins</button>
            <button class="Button" onclick="openViewLandlords()">View all Landlords</button>
            <button class="Button" onclick="openViewTenants()">View all Tenants</button>
            <button class="Button" onclick="openViewGuests()">View all Guests</button>   

            <div class="viewAllAdmins" id="viewAdminForm">
                <?php
                    $dbConn = getConnection();
                    $querySQL = "SELECT * FROM Account INNER JOIN Admin ON Account.accountID = Admin.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo "
                        <table>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created On</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        ";

                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo "
                                <tr>
                                    <th>$rowObj->username</th>
                                    <th>$rowObj->email</th>
                                    <th>$rowObj->creationDate</th>
                                    <th><a href='editAccount.php?accountID={$rowObj->accountID}' target='_blank'>Edit</a></th>
                                    <th><a href='deleteAccount.php?accountID={$rowObj->accountID}' target='_blank'>Remove</a></th>
                                </tr>
                            ";
                        }
                        echo"
                            </table>
                            <button type='button' class='btn cancel' onclick='closeViewAdmins()'>Close</button>
                        ";
                    }
                ?>
            </div>

            <div class="viewAllLandlords" id="viewLandlordForm">
                <?php
                    $dbConn = getConnection();
                    $querySQL = "SELECT * FROM Account INNER JOIN Landlord ON Account.accountID = Landlord.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo "
                        <table>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created On</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        ";

                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo "
                                <tr>
                                    <th>$rowObj->username</th>
                                    <th>$rowObj->email</th>
                                    <th>$rowObj->creationDate</th>
                                    <th><a href='editAccount.php?accountID={$rowObj->accountID}' target='_blank'>Edit</a></th>
                                    <th><a href='deleteAccount.php?accountID={$rowObj->accountID}' target='_blank'>Remove</a></th>
                                </tr>
                            ";
                        }
                        echo"
                            </table>
                            <button type='button' class='btn cancel' onclick='closeViewLandlords()'>Close</button>
                        ";
                    }
                ?>
            </div>

            <div class="viewAllTenants" id="viewTenantForm">
                <?php
                    $dbConn = getConnection();
                    $querySQL = "SELECT * FROM Account INNER JOIN Tenant ON Account.accountID = Tenant.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo "
                        <table>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created On</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        ";

                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo "
                                <tr>
                                    <th>$rowObj->username</th>
                                    <th>$rowObj->email</th>
                                    <th>$rowObj->creationDate</th>
                                    <th><a href='editAccount.php?accountID={$rowObj->accountID}' target='_blank'>Edit</a></th>
                                    <th><a href='deleteAccount.php?accountID={$rowObj->accountID}' target='_blank'>Remove</a></th>
                                </tr>
                            ";
                        }
                        echo"
                            </table>
                            <button type='button' class='btn cancel' onclick='closeViewTenants()'>Close</button>
                        ";
                    }
                ?>
            </div>

            <div class="viewAllGuests" id="viewGuestForm">
                <?php
                    $dbConn = getConnection();
                    $querySQL = "SELECT * FROM Account INNER JOIN Guest ON Account.accountID = Guest.accountID ORDER BY username ASC";
                    $queryResult = $dbConn->query($querySQL);

                    if($queryResult === false)
                    {
                        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                    }
                    else
                    {
                        echo "
                        <table>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created On</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        ";

                        while($rowObj = $queryResult->fetchObject())
                        {
                            echo "
                                <tr>
                                    <th>$rowObj->username</th>
                                    <th>$rowObj->email</th>
                                    <th>$rowObj->creationDate</th>
                                    <th><a href='editAccount.php?accountID={$rowObj->accountID}' target='_blank'>Edit</a></th>
                                    <th><a href='deleteAccount.php?accountID={$rowObj->accountID}' target='_blank'>Remove</a></th>
                                </tr>
                            ";
                        }
                        echo"
                            </table>
                            <button type='button' class='btn cancel' onclick='closeViewGuests()'>Close</button>
                        ";
                    }
                ?>
            </div>
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