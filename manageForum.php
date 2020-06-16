<?php


    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once("functions.php");
    checkPermission('Admin');

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
        <title>Manage Forum</title>
    </head>
    <body>

        <?php

            require_once("functions.php");
            checkLogin('index');

            buildNav();

            buildFooter();

            turnButtGreen('manageForum');

        ?>
        <div class="forumContent">
        <?php

            if ($_SESSION['loggedIn'] == true)
            {

                $checkAdmin = checkPermission('Admin');

                if ($checkAdmin == true) {

                    echo "<h2>Flagged Posts</h2>";

                    try {

                        $conn = getConnection();

                        $stmtSQL = "SELECT accountID, thread_id, thread_cat, thread_title, username, topic_date, topic_text FROM forum_threads
                                        JOIN Account on topic_by = accountID
                                        WHERE flagged = '1'
                                        ORDER BY topic_date DESC
                                        
                                        ";

                        $queryResult = $conn->query($stmtSQL);




                        while ($rowObj = $queryResult->fetchObject()) {




                                echo " 
                                            <div class=\"thread\">
                                                <p class='threadTitle'>{$rowObj->thread_title}</p>
                                                <hr>
                                                <p>{$rowObj->topic_text}</p>
                                                <hr>
                                                <p>Posted By: <span class='postedBy'>{$rowObj->username}</span> on {$rowObj->topic_date}</p>
                                                ";
                                                if($rowObj->thread_cat == 'rent'){
                                                    echo"<p>Category: (Renting) - Looking for a space to rent</p>";
                                                }
                                                else if ($rowObj->thread_cat == 'rentOut') {
                                                    echo"<p>Category: (Renting Out) - Space available here";
                                                }
                                                else if ($rowObj->thread_cat == 'question') {
                                                    echo"<p>Category: Question";
                                                }
                                                else if (empty($rowObj->thread_cat) || ($rowObj->thread_cat) == '') {
                                                    echo"<p>Category: No category assigned</p>";
                                                }
                                echo"
                                                <hr>
                                                
                                                <form action=\"deleteProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\">
                                                    <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                    <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                                </form>
                                            </div>
                                            <hr>    
                                        ";
                            }


                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        }


                    echo "<h2>Flagged Comments</h2>";

                    try {

                        $conn = getConnection();

                        $querySQL = "SELECT accountID, username, comment_id, thread_id, comment_text, comment_by, comment_date 
                          FROM forum_comments JOIN Account on comment_by = accountID
                          WHERE forum_comments.flagged = '1' 
                          ORDER BY comment_date DESC";

                        $queryResult = $conn->query($querySQL);

                        if($queryResult === false)
                        {
                            echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
                        }
                        else {
                            while ($rowObj = $queryResult->fetchObject()) {


                                echo " 
                                    <div class='comments'>
                                        <p>Comment: {$rowObj->comment_text}</p>
                                        <hr>
                                        <p>By: {$rowObj->username}</p>
                                        <hr>
                                        <p>Commented at: {$rowObj->comment_date}</p>
                                        <form action=\"deleteCommentsProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\">
                                            <input type=\"hidden\" name=\"comment_id\" value=\"$rowObj->comment_id\">
                                            <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                        </form>
                                    </div>
                                ";


                            }
                        }

                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                else {
                    echo "<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view this page</h2>";
                }


            }
            else {
                header("Location: index.php");
            }

        ?>



        </div>

    </body>
</html>