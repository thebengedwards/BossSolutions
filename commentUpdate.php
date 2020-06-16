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
        <title>Comment Update</title>
    </head>

    <body>
    <div class="forumContent">
        <?php

            $comment_id = filter_has_var(INPUT_GET, 'comment_id') ? $_GET['comment_id'] : null;
            $comment_text = filter_has_var(INPUT_GET, 'comment_text') ? $_GET['comment_text'] : null;

            $comment_id = trim($comment_id);
            $comment_text = trim($comment_text);

            if ($_SESSION['loggedIn'] == true) {

                $checkAdmin = checkPermission('Admin');
                $checkGuest = checkPermission('Guest');
                $checkTenant = checkPermission('Tenant');

                if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                    try {

                        $dbConn = getConnection();


                        $comment_date = date('Y-m-d H:i:s');


                        // prepare sql and bind parameters
                        $stmt = $dbConn->prepare("UPDATE forum_comments SET comment_text=:comment_text, comment_date=:comment_date
                                                      WHERE comment_id =:comment_id
                                                      ");
                        $stmt->bindParam(':comment_text', $comment_text);
                        $stmt->bindParam(':comment_date', $comment_date);
                        $stmt->bindParam(':comment_id', $comment_id);

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
                }
                else {
                    echo "<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
                }
            }
            else {
                header("Location: index.php");
            }

            $dbConn = null;

        ?>
    </div>
    </body>
</html>
