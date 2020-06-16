<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <link rel=meta charset='UTF-8' />
    <link rel='stylesheet' type='text/css' href='stylesheet.css'>
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
    <title>Send Email to all users process</title>
</head>
<body>

    <?php

    $subject = filter_has_var(INPUT_POST, 'subject') ? $_POST['subject']: null;
    $subject = trim($subject);
    $message = filter_has_var(INPUT_POST, 'message') ? $_POST['message']: null;
    $message = trim($message);

    require_once("functions.php");
    $dbConn = getConnection();
    
    $querySQL = "SELECT email FROM Account";
    $queryResult = $dbConn->query($querySQL);

    if($queryResult === false)
    {
        echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
    }
    else
    {
        while($rowObj = $queryResult->fetchObject())
        {
            
            $from = "NoReply@BossSolutions.com";
            $to = $rowObj->email;
            
            $headers = "From:" . $from;
            mail($to,$subject,$message,$headers);

            header('Location: mailSystem.php');
        }
    }
    ?>

</body>
</html>