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
        <link rel="stylesheet" type="text/css" href="forumStyle.css">
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
        $thread_id = filter_has_var(INPUT_POST, 'thread_id') ? $_POST['thread_id'] : null;
        $thread_id = trim($thread_id);


        $commentText = filter_has_var(INPUT_POST, 'comment_text') ? $_POST['comment_text'] : null;
        $commentText = trim($commentText);

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {
                if (empty($commentText)) {
                    echo "<p>Please enter a comment</p>\n";
                    $errors = true;
                } else if (strlen($commentText) > 1000) {
                    echo "<p>Please enter a comment with less than 1000 characters</p>\n";
                    $errors = true;
                }

                if ($errors === true) {
                    echo "<p>You didn't fill in the form correctly, please try <a href='javascript:history.back()'>again</a>.</p>\n";
                }
                else {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                        if (!empty($thread_id)) {
                            try {

                                $dbConn = getConnection();

                                $date = date('Y-m-d H:i:s');

                                // prepare sql and bind parameters
                                $stmt = $dbConn->prepare("INSERT INTO forum_comments (thread_id, comment_text, comment_by, comment_date) 
                                                      VALUES (:thread_id, :comment_text, :comment_by, :comment_date)");
                                $stmt->bindParam(':thread_id', $thread_id);
                                $stmt->bindParam(':comment_text', $commentText);
                                $stmt->bindParam(':comment_by', $id);
                                $stmt->bindParam(':comment_date', $date);

                                // insert a row
                                $thread_id = $_POST["thread_id"];
                                //$commentText = $_POST["comment_text"];
                                $id = $_SESSION['userID'];
                                $stmt->execute();

                                $referer = $_SERVER['HTTP_REFERER'];
                                header("Location: $referer");

                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                            $dbConn = null;
                        } else {
                            echo "<p>You cannot post a comment on a non-existing post. Click<a href='forum.php'> HERE</a> to go back.</p>";
                        }

                    }
                }
            }
            else {
                echo"<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
            }

        }
        else{
            header("Location: index.php");
        }
    ?>
    </div>
    </body>
</html>
