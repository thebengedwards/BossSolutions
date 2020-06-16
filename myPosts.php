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
        <title>Forum My Posts</title>
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

                try {

                    $conn = getConnection();

                    $stmtSQL = "SELECT accountID, thread_id, thread_cat, thread_title, username, topic_date, topic_text FROM forum_threads
                                            JOIN Account on topic_by = accountID
                                            ORDER BY topic_date DESC
                                            ";

                    $queryResult = $conn->query($stmtSQL);

                    echo "<h2>My Posts</h2>";


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
                                                <a href='comments.php?thread_id={$rowObj->thread_id}' class='forumCommentButton'>Comments</a>
                                                <a href='threadEdit.php?thread_id={$rowObj->thread_id}' class='forumEditButton'>Edit</a>
                                             
                                                <form action=\"deleteProcess.php\" method=\"post\" class=\"forumDeleteFlagForm\">
                                                    <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                    <input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete this?')\" name=\"submit\" value=\"Delete/Resolve\">
                                                </form>
                                                ";


                                            if ($rowObj->thread_cat == 'rentOut') {

                                            echo "
                                                
                                                <form action=\"\" method=\"post\" class=\"forumUploadForm\" enctype=\"multipart/form-data\">
                                                    <input type=\"hidden\" name=\"thread_id\" value=\"$rowObj->thread_id\">
                                                    <input type='file' name='files[]' multiple />
                                                    <input type=\"submit\" name=\"submits\" value='Upload'>
                                                </form>
                
                                                
                                            
                                           
                                                ";
                                            }
                                            echo "
                                                   </div>
                                                   <hr>";

                            if (isset($_POST['submits'])) {
                                $thread_id = filter_has_var(INPUT_POST, 'thread_id') ? $_POST['thread_id'] : null;
                                $countfiles = count($_FILES['files']['name']);

                                $query = "INSERT INTO forum_images (name, image, tID) VALUES( ?, ?, '$thread_id')";
                                $statement = $conn->prepare($query);

                                for ($i = 0; $i < $countfiles; $i++) {

                                    $filename = $_FILES['files']['name'][$i];
                                    $target_file = 'forumImages/' . $filename;
                                    $file_extensions = pathinfo($target_file, PATHINFO_EXTENSION);
                                    $file_extensions = strtolower($file_extensions);

                                    $file_types = array("png", "jpeg", "jpg", "jfif");

                                    if (in_array($file_extensions, $file_types)) {

                                        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_file)) {

                                            $statement->execute(array($filename, $target_file));

                                        }
                                    }

                                }
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

        }
        else {
            header("Location: index.php");
        }

    ?>

    </div>
    </body>
</html>