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
            checkLogin('index');

            buildNav();

            buildFooter();
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <title>Forum Thread Update</title>
    </head>

    <body>
        <div class="forumContent">
        <?php


            $thread_id = filter_has_var(INPUT_GET, 'thread_id') ? $_GET['thread_id'] : null;
            $thread_title = filter_has_var(INPUT_GET, 'thread_title') ? $_GET['thread_title'] : null;
            $topic_text = filter_has_var(INPUT_GET, 'topic_text') ? $_GET['topic_text'] : null;
            $topic_address = filter_has_var(INPUT_GET, 'topic_address') ? $_GET['topic_address'] : null;


            $thread_title = trim($thread_title);
            $topic_text = trim($topic_text);
            $thread_id = trim($thread_id);
            $topic_address = trim($topic_address);
            

            if ($_SESSION['loggedIn'] == true) {

                $checkAdmin = checkPermission('Admin');
                $checkGuest = checkPermission('Guest');
                $checkTenant = checkPermission('Tenant');

                if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {


                    try {

                        $dbConn = getConnection();


                        $topic_date = date('Y-m-d H:i:s');


                        // prepare sql and bind parameters
                        $stmt = $dbConn->prepare("UPDATE forum_threads SET thread_title=:thread_title, topic_text=:topic_text, topic_date=:topic_date, topic_address=:topic_address
                                              WHERE thread_id =:thread_id
                                              ");
                        $stmt->bindParam(':thread_title', $thread_title);
                        $stmt->bindParam(':topic_text', $topic_text);
                        $stmt->bindParam(':thread_id', $thread_id);
                        $stmt->bindParam(':topic_date', $topic_date);
                        $stmt->bindParam(':topic_address', $topic_address);


                        // insert a row

                        $stmt->execute();
                        if ($stmt->execute()) {
                            echo "<h2>You have successfully updated your post - click: <a href=\"javascript:history.go(-2)\">HERE</a> to go back</h2>";
                        } else {
                            echo "<h2>There was an error in updating your post - click: <a href=\"javascript:history.go(-2)\">HERE</a> to go back</h2>";
                        }


                    } catch (Exception $e) {
                        echo "<p>thread details not found: " . $e->getMessage() . "</p>";
                    }
                    $dbConn = null;
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
