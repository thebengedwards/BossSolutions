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
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <title>Forum Rent Out</title>
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

                echo '<h2>Renting Out</h2>
    
                    <hr>
                    
                    <form action="forumRentOutProcess.php" method="post" class="forumPostForm">
                        <p>Title: </p><input type="text" name="thread_title" required><br>
                        <p>Content: </p><textarea type="text" name="topic_text" required></textarea><br>
                        <p>Location of Property </p><input type="text" name="topic_address"><br>
                        <p>Upload images in your Posts section</p>
                        <input type="submit">
                    </form>
                    
                    
                    <h2>Feed</h2>
                    <hr>
                 ';

                try {

                    $conn = getConnection();

                    $stmtSQL = "SELECT accountID, thread_id, thread_cat, thread_title, username, topic_date, topic_text, topic_address FROM forum_threads
                                JOIN Account on topic_by = accountID
                                WHERE thread_cat = 'rentOut'
                                ORDER BY topic_date DESC
                                ";

                    $queryResult = $conn->query($stmtSQL);

                    while ($rowObj = $queryResult->fetchObject()) {


                        if ($_SESSION['userID'] == $rowObj->accountID) {
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


                                    if ($rowObj->topic_address !== '') {
                                        echo "<p>Location: {$rowObj->topic_address}</p>";
                                    } else if ($rowObj->topic_address == '' && $rowObj->thread_cat == 'rentOut') {
                                        echo "<p>Location: The poster did not input an address</p>";
                                    }
                            echo "

                                    <hr>
                                    <a href='comments.php?thread_id={$rowObj->thread_id}' class='forumCommentButton'>Comment</a>
                                    <a href='threadEdit.php?thread_id={$rowObj->thread_id}' class='forumEditButton'>Edit</a>
                                        
                                    <form action=\"deleteProcess.php\" method=\"post\" class='forumDeleteFlagForm'>
                                        <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                        <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                    </form>
                                </div>
                                <hr>
                            ";

                        } else {
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
                                    echo"<p>Category: (Renting Out) - Space available here</p>";
                                }
                                else if ($rowObj->thread_cat == 'question') {
                                    echo"<p>Category: Question</p>";
                                }
                                else if (empty($rowObj->thread_cat) || ($rowObj->thread_cat) == '') {
                                    echo"<p>Category: No category assigned</p>";
                                }

                            echo"
                                <hr>
                                
                                <a href='comments.php?thread_id={$rowObj->thread_id}' class='forumCommentButton'>Comment</a>

                                <form action=\"flagPostProcess.php\" method=\"post\" class='forumDeleteFlagForm'>
                                    <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                    <input type=\"submit\" onclick=\"return confirm('Are you sure you want to flag this?')\" name=\"submit\" value=\"Flag\">
                                </form>
                            
                            </div>

                            <hr>
                      
                            ";
                        }
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
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
