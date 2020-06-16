<?php
    require_once ("functions.php");
    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();

    if ($_SESSION['loggedIn'] == true) {

        $checkAdmin = checkPermission('Admin');
        $checkGuest = checkPermission('Guest');
        $checkTenant = checkPermission('Tenant');

        if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {

                    $dbConn = getConnection();


                    // prepare sql and bind parameters
                    $stmt = $dbConn->prepare("UPDATE forum_threads SET flagged = '1' WHERE thread_id = :thread_id");
                    $stmt->bindParam(':thread_id', $thread_id);


                    // insert a row
                    $thread_id = $_POST["thread_id"];
                    $stmt->execute();

                    $referer = $_SERVER['HTTP_REFERER'];
                    header("Location: $referer");

                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                $dbConn = null;
            }
        }
        else {
            header("Location: index.php");
        }
    }
    else {
        header("Location: index.php");
    }

?>
