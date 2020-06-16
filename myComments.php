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

        buildPage();
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <title>Forum My Comments</title>
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

        if ($_SESSION['loggedIn'] == true)
        {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {
                echo "<h2>My Comments</h2>";

                try {

                    $conn = getConnection();

                    $querySQL = "SELECT accountID, username, comment_id, thread_id, comment_text, comment_by, comment_date 
                                          FROM forum_comments JOIN Account on comment_by = accountID
                                          ORDER BY comment_date DESC";

                    $queryResult = $conn->query($querySQL);

                    if ($queryResult === false) {
                        echo "<p>Query failed: " . $dbConn->error . "</p>\n</body>\n</html>";
                    } else {
                        while ($rowObj = $queryResult->fetchObject()) {

                            if ($_SESSION['userID'] == $rowObj->accountID) {


                                echo " 
                                                        <div class='comments'>
                                                            <p>Comment: {$rowObj->comment_text}</p>
                                                            <hr>
                                                            <p>By: {$rowObj->username}</p>
                                                            <hr>
                                                            <p>Commented at: {$rowObj->comment_date}</p>
                                                            
                                                            <a href='commentEdit.php?comment_id={$rowObj->comment_id}' class='forumEditButton'>Edit</a>
                                                            <form action=\"deleteCommentsProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\">
                                                                <input type=\"hidden\" name=\"comment_id\" value=\"$rowObj->comment_id\">
                                                                <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete/Resolve\">
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
            else {
                echo"<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
            }

        }else {
            header("Location: index.php");
        }

    ?>
    </div>
    </body>
</html>