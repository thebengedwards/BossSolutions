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
        <title>Forum Edit Your Post</title>
    </head>
    <body>
        <div class="forumContent">
        <?php
            $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
            

            if ($_SESSION['loggedIn'] == true) {

                $checkAdmin = checkPermission('Admin');
                $checkGuest = checkPermission('Guest');
                $checkTenant = checkPermission('Tenant');

                if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                    if (empty($thread_id)) {
                        echo "<p>Please <a href='forum.php'>choose</a> a post.</p>\n";
                    } else {
                        try {


                            require_once("functions.php");
                            $dbConn = getConnection();
                            $sqlQuery = "SELECT accountID, thread_id, thread_cat, thread_title, username, topic_date, topic_text, topic_address FROM forum_threads
                                  JOIN Account on topic_by = accountID
                                WHERE thread_id = $thread_id";

                            $queryResult = $dbConn->query($sqlQuery);
                            $rowObj = $queryResult->fetchObject();


                            echo "
                        <h2>Edit your post</h2>
                        <form id='updateThread' action='threadUpdate.php' method='get' class='forumPostForm'>
                            <input type='text' name='thread_id' value='$thread_id' HIDDEN />
                            <p>Title: </p><input type='text' name='thread_title' value='{$rowObj->thread_title}' />
                            <p>Content: </p><textarea type='text' name='topic_text'>{$rowObj->topic_text}</textarea>
                            ";

                            if (($rowObj->thread_cat) == "rentOut") {
                                if (empty($rowObj->topic_address)) {
                                    echo "<p>Address: </p><input type='text' name='topic_address' value='' />";
                                } else if (!empty($rowObj->topic_address)) {
                                    echo "<p>Address: </p><input type='text' name='topic_address' value='{$rowObj->topic_address}' />";
                                }
                            }

                            echo "
                            <input type='submit' name='submit' value='Update Thread'>
                        </form>
                        ";


                        } catch (Exception $e) {
                            echo "<p>Property details not found: " . $e->getMessage() . "</p>";
                        }
                    }
                    echo "</div>";

                }
                else {
                    echo"<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
                }
            }
            else {
                header("Location: index.php");
            }
        ?>
        </div>
    </body>
</html>