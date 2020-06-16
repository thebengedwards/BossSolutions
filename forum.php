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
        <title>Forum</title>
    </head>

    <body>

        <?php

            require_once("functions.php");
            checkLogin('index');

            buildNav();

            buildFooter();

            turnButtGreen('forum');

        ?>

        <h2>Forum</h2>
            <div id="centerDiv">
                <div class="forumNav">


                    <a href="forumRentOut.php" class="forumNavCat">
                        <h2>Looking to Rent out</h2>
                        <p>Thread for current rents that have spaces available</p>
                    </a>

                    <a href="forumRent.php" class="forumNavCat">
                        <h2>Looking To Rent</h2>
                        <p>Thread for people looking to rent spaces</p>
                    </a>

                    <a href="forumQuestions.php" class="forumNavCat">
                        <h2>Questions</h2>
                        <p>Miscellaneous threads</p>
                    </a>

                    <a href="myPosts.php" class="forumNavCat">
                        <h2>Posts</h2>
                        <p>A list of your Posts</p>
                    </a>

                    <a href="myComments.php" class="forumNavCat">
                        <h2>Comments</h2>
                        <p>A list of your comments</p>
                    </a>

                </div>
            </div>
        <hr>







        <?php

            if ($_SESSION['loggedIn'] == true) {

                $checkAdmin = checkPermission('Admin');
                $checkGuest = checkPermission('Guest');
                $checkTenant = checkPermission('Tenant');

                if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                    echo "<div class='forumContent'><h2>All Feed</h2>";
                    try {

                        $conn = getConnection();

                        $stmtSQL = "SELECT accountID, thread_id, thread_cat, thread_title, username, topic_date, topic_text, topic_address FROM forum_threads
                                    JOIN Account on topic_by = accountID
                                    ORDER BY topic_date DESC
                                    ";

                        $queryResult = $conn->query($stmtSQL);

                        while ($rowObj = $queryResult->fetchObject()) {

                            //if the post is posted by the account signed in, then display the delete button
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
                                            echo"<p>Location: {$rowObj->topic_address}</p>";
                                        }
                                        else if ($rowObj->topic_address == '' && $rowObj->thread_cat == 'rentOut') {
                                            echo "<p>Location: There poster did not input an address</p>";
                                        }

                                echo "
    
                                        <hr>
                                        <div class='postOptions'>
                                        
                                            <a href='comments.php?thread_id={$rowObj->thread_id}' class='forumCommentButton'>Comment</a>
                                            <a href='threadEdit.php?thread_id={$rowObj->thread_id}' class='forumEditButton'>Edit</a>
                                        
                                       
                                            <form action=\"deleteProcess.php\" method=\"post\" class='forumDeleteFlagForm'>
                                                <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete\">
                                            </form>
                                          
                                        </div>
        
                                    </div>
                                    <hr>
                                    
        
                                ";


                            }// if the user signed in is not the poster, then display the flag button
                            else {
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
                                    echo"<p>Location: {$rowObj->topic_address}</p>";
                                }
                                else if ($rowObj->topic_address == '' && $rowObj->thread_cat == 'rentOut') {
                                    echo "<p>Location: The poster did not input an address</p>";
                                }

                                echo "
                                        <hr>
                                        <a href='comments.php?thread_id={$rowObj->thread_id}' class='forumCommentButton'>Comment</a>
                                        
                                        
                                            <form action=\"flagPostProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\" id=\"forumFlag\">
                                                <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                <input type=\"submit\" onclick=\"return confirm('Are you sure you want to flag this?')\" name=\"submit\" value=\"Flag\">
                                            </form>
                                        
                                        
                                    </div>
                              
                                
                                
                              
                                ";
                            }
                        }
                        echo "</div>";

                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                else {
                    echo "<h2 id='forumNotLoggedInMsg'>Your account role does not have the privileges to view the forum</h2>";
                }

            }
            else {
                echo "<h2 id='forumNotLoggedInMsg'>Make an Account to interact with the above features of the forum</h2>";
            }

        ?>

    </body>
</html>
