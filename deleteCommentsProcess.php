<?php

    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once ("functions.php");



    if ($_SESSION['loggedIn'] == true) {

        $checkAdmin = checkPermission('Admin');
        $checkGuest = checkPermission('Guest');
        $checkTenant = checkPermission('Tenant');

        if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {

                    $dbConn = getConnection();


                    // prepare sql and bind parameters
                    $stmt = $dbConn->prepare("DELETE FROM forum_comments WHERE comment_id = :comment_id");
                    $stmt->bindParam(':comment_id', $comment_id);


                    // insert a row
                    $comment_id = $_POST["comment_id"];
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
