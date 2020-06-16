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
    <title>Create Guest Page</title>
</head>
<body>

    <?php

        require_once('functions.php');
        checkLogin('index');
    
        buildNav();
    
        buildFooter();

    ?>

    <div class="accManagment">
        <h3>Create a Guest Account</h3>
        <p>Please Type in your details below:</p>

        <div class="createNewAccount" id="createForm">
            <div class="selfCreateNewGuest" id="createGuestForm">
                <form method="post" action="createGuestProcess.php" class="create-container">
                    <h1>Create New Guest</h1>

                    <label for="username"><b>Username:</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>

                    <label for="email"><b>Email:</b></label>
                    <input type="text" placeholder="Enter Email" name="email" required>

                    <label for="password"><b>Password:</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>

                    <hr>
                    
                    <button type="submit" class="btn" value="logon">Create Guest Account</button>
                </form>
            </div>
        </div>    
    </div>

</body>
</html>