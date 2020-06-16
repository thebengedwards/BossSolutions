<?php

    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <?php
        require_once("functions.php");
        buildPage();
        checkLogin('index');

        buildNav();

        buildFooter();
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <title>Forum Edit Your Comment</title>
    </head>
    <body>

        <div class="forumContent">
            <?php
                $comment_id = filter_has_var(INPUT_GET, 'comment_id') ? $_GET['comment_id'] : null;
                $comment_id = trim($comment_id);

                if (empty($comment_id)) {
                    echo "<p>Please <a href='forum.php'>choose</a> a post.</p>\n";
                } else {

                    if ($_SESSION['loggedIn'] == true) {

                        $checkAdmin = checkPermission('Admin');
                        $checkGuest = checkPermission('Guest');
                        $checkTenant = checkPermission('Tenant');

                        if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {
                            try {

                                require_once("functions.php");
                                $dbConn = getConnection();
                                $sqlQuery = "SELECT accountID, username, comment_id, thread_id, comment_text, comment_by, comment_date 
                                      FROM forum_comments JOIN Account on comment_by = accountID
                                      WHERE comment_id = '$comment_id' 
                                      ";

                                $queryResult = $dbConn->query($sqlQuery);
                                $rowObj = $queryResult->fetchObject();

                                echo "
                            
                            <h2>Update your comment</h2>
                            <form id='updateThread' action='commentUpdate.php' method='get' class='forumPostForm'>
                            
                                <input type='text' name='comment_id' value='$comment_id' HIDDEN />
                                <p>Comment: </p><input type='text' name='comment_text' value='{$rowObj->comment_text}' />
                                <input type='submit' name='submit' value='Update Comment'>
                            </form>
                    
                        ";


                            } catch (Exception $e) {
                                echo "<p>Property details not found: " . $e->getMessage() . "</p>";
                            }
                        }
                        else {
                            echo "<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
                        }
                    }
                    else {
                        header("Location: index.php");
                    }
                }
                echo"</div>";
            echo"</div>";

        ?>

    </body>
</html>