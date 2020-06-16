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
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
        <title>Forum Questions</title>
    </head>
    <body>

    <?php

        require_once("functions.php");
        checkLogin('index');

        buildNav();

        buildFooter();

    ?>



    <?php

        $threadTitle = filter_has_var(INPUT_POST, 'thread_title') ? $_POST['thread_title'] : null;
        $threadTitle = trim($threadTitle);

        $topic_text = filter_has_var(INPUT_POST, 'topic_text') ? $_POST['topic_text'] : null;
        $topic_text = trim($topic_text);

        if ($_SESSION['loggedIn'] == true) {

            $checkAdmin = checkPermission('Admin');
            $checkGuest = checkPermission('Guest');
            $checkTenant = checkPermission('Tenant');

            if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

                if (empty($threadTitle)) {
                    echo "<p>The thread title cannot be blank</p>\n";
                    $errors = true;
                } else if (strlen($threadTitle) > 80) {
                    echo "<p>Please enter a title with less than 80 characters</p>\n";
                    $errors = true;
                }

                if (empty($topic_text)) {
                    echo "<p>Please enter some content in the body of the thread</p>\n";
                    $errors = true;
                } else if (strlen($topic_text) > 1500) {
                    echo "<p>Please enter content with less than 1500 characters</p>\n";
                    $errors = true;
                }

                if ($errors === true) {
                    echo "<p>You didn't fill in the form correctly, please try <a href='forumQuestions.php'>again</a>.</p>\n";
                }
                else {

                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        try {

                            $dbConn = getConnection();

                            $cat = "question";
                            $date = date('Y-m-d H:i:s');


                            // prepare sql and bind parameters
                            $stmt = $dbConn->prepare("INSERT INTO forum_threads (thread_cat, thread_title, topic_text, topic_by, topic_date) 
                                                              VALUES (:thread_cat, :thread_title, :topic_text, :topic_by, :topic_date)");
                            $stmt->bindParam(':thread_cat', $cat);
                            $stmt->bindParam(':thread_title', $threadTitle);
                            $stmt->bindParam(':topic_text', $topicText_clean);
                            $stmt->bindParam(':topic_by', $id);
                            $stmt->bindParam(':topic_date', $date);

                            // insert a row
                            $cat = "question";
                            //$threadTitle = $_POST["thread_title"];
                            //$topicText = $_POST["topic_text"];
                            $topicText_clean = nl2br(strip_tags($topic_text));
                            $id = $_SESSION['userID'];
                            $stmt->execute();


                            $referer = $_SERVER['HTTP_REFERER'];
                            header("Location: $referer");
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        $dbConn = null;
                    }
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

    </body>
</html>
