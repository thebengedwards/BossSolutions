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
    <title>Send Email to property process</title>
</head>
<body>

    <?php

    $propertyID = filter_has_var(INPUT_POST, 'propertyID') ? $_POST['propertyID']: null;
    $propertyID = trim($propertyID);
    $subject = filter_has_var(INPUT_POST, 'subject') ? $_POST['subject']: null;
    $subject = trim($subject);
    $message = filter_has_var(INPUT_POST, 'message') ? $_POST['message']: null;
    $message = trim($message);
    
    if($propertyID == 'NULL')
    {
        header('Location: mailSystem.php');
    }
    else
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT accountID FROM Tenant WHERE propertyID='$propertyID'";
        $queryResult = $dbConn->query($querySQL);

        if($queryResult === false)
        {
            echo "<p>Query failed: ".$dbConn->error."</p>\n</body>\n</html>";
        }
        else
        {
            while($rowObj = $queryResult->fetchObject())
            {
                $accountID=$rowObj->accountID;

                $dbConn1 = getConnection();
        
                $querySQL1 = "SELECT email FROM Account WHERE accountID='$accountID'";
                $queryResult1 = $dbConn->prepare($querySQL1);
                $queryResult1 -> execute(array(':accountID'=>$accountID));
                $rowObj1 = $queryResult1->fetchObject();
                
                $from = "NoReply@BossSolutions.com";
                $to = $rowObj1->email;
            
                $headers = "From:" . $from;
                mail($to,$subject,$message,$headers);

                header('Location: mailSystem.php');
            }
        }
    }

    ?>

</body>
</html>