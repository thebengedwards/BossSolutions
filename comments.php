<?php


    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once("functions.php");
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <?php
            require_once("functions.php");
            buildPage();
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script type="text/javascript" src="gmap.js"></script>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <script type="text/javascript" src="geocode.js"></script>
        <script type="text/javascript" src="address.js"></script>

        <title>Forum Comments</title>
    </head>
    <body>

    <?php

        require_once("functions.php");
        checkLogin('index');

        buildNav();

        buildFooter();



    ?>


    <div class="forumContent">
    <?php

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {


                try {

                    $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
                    $thread_id = trim($thread_id);

                    $conn = getConnection();

                    $stmtSQL = "SELECT accountID, thread_id, thread_title, username, topic_date, topic_text FROM forum_threads
                                JOIN Account on topic_by = accountID
                                WHERE thread_id = '$thread_id'
                                ORDER BY topic_date DESC
                                ";

                    $queryResult = $conn->query($stmtSQL);

                    while ($rowObj = $queryResult->fetchObject()) {
                        echo "
                                <h1 class='cThreadTitle'>{$rowObj->thread_title}</h1>
                                <hr>
                                <div class=\"thread\">
                                    <p>{$rowObj->topic_text}</p>
                                    <hr>
                                    <p>Posted By: {$rowObj->username} on {$rowObj->topic_date}</p>
                                
                             ";

                        if ($_SESSION['userID'] == $rowObj->accountID) {
                            echo "
                                                 
                                                 <a href='threadEdit.php?thread_id={$rowObj->thread_id}' class='forumEditButton'>Edit</a>
                                                 
                                                <form action=\"deleteProcessCom.php\" method=\"post\" class=\"forumDeleteFlagForm\">
                                                        <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                        <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                                </form>
                                                
                                        ";
                        } else {
                            echo "
                                       
                          
                                                <form action=\"flagCommentsProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\" id=\"forumFlag\">
                                                    <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                    <input type=\"submit\" onclick=\"return confirm('Are you sure you want to flag this?')\" name=\"submit\" value=\"Flag\">
                                                </form>
                                                ";
                        }
                        echo "
                            </div>
    
                            <hr>
                            
                            ";
                    }
                    $_SESSION['thread_id'] = $thread_id;

                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
        else {
            header("Location: index.php");
        }
    ?>
<!--------------------------------------------------------------------------------------------------->
        <?php

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {


                try {

                    $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
                    $thread_id = trim($thread_id);

                    $conn = getConnection();

                    $stmtSQL = "SELECT thread_id, image FROM forum_threads
                                JOIN forum_images on tID = thread_id
                                WHERE tID = '$thread_id'
                                
                                ";

                    $queryResult = $conn->query($stmtSQL);

                    while ($rowObj = $queryResult->fetchObject()) {
                        echo "
                                <img src='$rowObj->image' style='max-width: 500px;'>
                             ";

                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

            } else {
                header("Location: index.php");
            }
        }
        ?>


    <!-- SET MAPS TO BE VISIBLE IF THE CATEGORY IS RENT OUT AND THE ADDRESS IS SET -->
    <?php

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                try {

                    $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
                    $thread_id = trim($thread_id);

                    $conn = getConnection();

                    $stmtSQL = "SELECT thread_id, thread_cat, topic_address FROM forum_threads WHERE thread_id = '$thread_id'
                                    ";

                    $queryResult = $conn->query($stmtSQL);

                    //$rowObj = $queryResult->fetchObject();

                    while ($rowObj = $queryResult->fetchObject()) {
                        if ($rowObj->topic_address !== '' && $rowObj->thread_cat == 'rentOut') {
                            echo "
                            <div class='mapMsg'>
                                <p>Located at: {$rowObj->topic_address}</p>
                            </div>
                            <div id=\"map\"></div>

                            <script async defer src=\"https://maps.googleapis.com/maps/api/js?key=APIKEYHERE&callback=initMap\"></script>
                            <hr>
                        ";
                        } else if ($rowObj->topic_address == '' && $rowObj->thread_cat == 'rentOut') {
                            echo "
                        <div class='noMapMsg'>
                            <p>The poster of this thread didn't input a location</p>
                        </div>
                    ";

                        }
                    }


                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
            else {
                echo"Your account role does not have the privileges to view this page";
            }
        }
        else {
            header("Location: index.php");
        }
    ?>



<!-- Display the form section -->


    <?php

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
                $thread_id = trim($thread_id);

                echo "
                    <form action=\"forumCommentProcess.php\" method=\"post\" class=\"forumPostForm\">
                        <p>Comment below:</p>
                        <input type='hidden' value='$thread_id' READONLY name='thread_id'>
                        <input type=\"text\" name=\"comment_text\" required><br>
                    
                    
                        <input type=\"submit\">
                    </form>
                
                ";
            }

        }
        else {
            header("Location: index.php");
        }
    ?>

<!-- Display the actual comments of the post section -->

    <?php
        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                try {


                    $conn = getConnection();


                    $querySQL = "SELECT accountID, username, comment_id, thread_id, comment_text, comment_by, comment_date 
                              FROM forum_comments JOIN Account on comment_by = accountID
                              WHERE thread_id = '$thread_id' 
                              ORDER BY comment_date DESC";
                    $queryResult = $conn->query($querySQL);

                    if ($queryResult === false) {
                        echo "<p>Query failed: " . $dbConn->error . "</p>\n</body>\n</html>";
                    } else {
                        while ($rowObj = $queryResult->fetchObject()) {


                            if ($_SESSION['userID'] == $rowObj->accountID) {
                                echo " <div class='comments'>
                                        <p>Comment: {$rowObj->comment_text}</p>
                                        <hr>
                                        <p>By: {$rowObj->username}</p>
                                        <hr>
                                        <p>Commented at: {$rowObj->comment_date}</p>
                                        
                                        <a href='commentEdit.php?comment_id={$rowObj->comment_id}' class='forumEditButton'>Edit</a>
                                        
                                        <form action=\"deleteCommentsProcess.php\" method=\"post\" class='forumDeleteFlagForm'>
                                            <input type=\"hidden\" name=\"comment_id\" value=\"$rowObj->comment_id\">
                                            <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                        </form>
                    
                                    </div>
                                    
                                    
                                ";

                            } else {
                                echo "
                                <div class='comments'>
                                    <p>Comment: {$rowObj->comment_text}</p>
                                    <hr>
                                    <p>By: {$rowObj->username}</p>
                                    <hr>
                                    <p>Commented at: {$rowObj->comment_date}</p>
                                  
                                  
                                    <form action=\"flagCommentsProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\" id=\"forumFlag\">
                                        <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                        <input type=\"submit\" onclick=\"return confirm('Are you sure you want to flag this?')\" name=\"submit\" value=\"Flag\">
                                    </form>
                                            
                                </div>
                                
                              
                                    
                              ";
                            }

                        }
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
        else {
            header("Location: index.php");
        }

    ?>

    </div>
    </body>
</html>
