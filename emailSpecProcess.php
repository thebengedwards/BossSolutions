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
    <title>Send Email to specific users process</title>
</head>
<body>

    <?php

    $accountType = filter_has_var(INPUT_POST, 'accountType') ? $_POST['accountType']: null;
    $accountType = trim($accountType);
    $subject = filter_has_var(INPUT_POST, 'subject') ? $_POST['subject']: null;
    $subject = trim($subject);
    $message = filter_has_var(INPUT_POST, 'message') ? $_POST['message']: null;
    $message = trim($message);

    if($accountType == 'admin')
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT email FROM Account INNER JOIN Admin ON Account.accountID = Admin.accountID";
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
    }
    else if($accountType == 'landlord')
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT email FROM Account INNER JOIN Landlord ON Account.accountID = Landlord.accountID";
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
    }
    else if($accountType == 'tenant')
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT email FROM Account INNER JOIN Tenant ON Account.accountID = Tenant.accountID";
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
    }
    else if($accountType == 'guest')
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT email FROM Account INNER JOIN Guest ON Account.accountID = Guest.accountID";
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
    }
    else
    {
        echo"Error, account type not detected";
        header('Location: mailSystem.php');
    }

    ?>

</body>
</html>