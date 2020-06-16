<?php
    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once("functions.php");


    $thread_id = $_SESSION['thread_id'];


    if ($_SESSION['loggedIn'] == true)
    {

        $checkAdmin = checkPermission('Admin');
        $checkGuest = checkPermission('Guest');
        $checkTenant = checkPermission('Tenant');

        if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {


            try {
                $conn = getConnection();

                $statement = $conn->prepare("SELECT topic_address FROM forum_threads WHERE thread_cat = 'rentOut' AND thread_id = '$thread_id' AND topic_address != ''");
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                $json = json_encode($results);

                echo $json;
            }
            catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        else {
            echo"<h2 id='forumNotLoggedInMsg'>Your account does not have the privileges to view to this page</h2>";
        }
    }
    else {
        header("Location: index.php");
    }


?>