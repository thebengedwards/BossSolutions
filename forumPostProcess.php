<?php

    ini_set("session.save_path", "/home/unn_w17004394/sessionData");
    session_start();
    require_once("functions.php");
?>



<?php

    require_once("functions.php");
    checkLogin('index');

    buildNav();

    buildFooter();

?>


<?php

    if ($_SESSION['loggedIn'] == true) {

        $checkAdmin = checkPermission('Admin');
        $checkGuest = checkPermission('Guest');
        $checkTenant = checkPermission('Tenant');

        if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {

                    $dbConn = getConnection();


                    $date = date('Y-m-d H:i:s');

        // prepare sql and bind parameters
                    $stmt = $dbConn->prepare("INSERT INTO forum_threads (thread_title, topic_text, topic_by, topic_date) 
                                                  VALUES (:thread_title, :topic_text, :topic_by, :topic_date)");
                    $stmt->bindParam(':thread_title', $threadTitle);
                    $stmt->bindParam(':topic_text', $topicText);
                    $stmt->bindParam(':topic_by', $id);
                    $stmt->bindParam(':topic_date', $date);

        // insert a row
                    $threadTitle = $_POST["thread_title"];
                    $topicText = $_POST["topic_text"];
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
        else {
            header("Location: index.php");
        }
    }
    else {
        header("Location: index.php");
    }

?>

