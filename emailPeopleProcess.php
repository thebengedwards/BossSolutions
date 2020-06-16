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
    <title>Send Email to People process</title>
</head>
<body>

    <?php

    $adminUserID = filter_has_var(INPUT_POST, 'adminUserID') ? $_POST['adminUserID']: null;
    $adminUserID = trim($adminUserID);
    $landlordUserID = filter_has_var(INPUT_POST, 'landlordUserID') ? $_POST['landlordUserID']: null;
    $landlordUserID = trim($landlordUserID);
    $tenantUserID = filter_has_var(INPUT_POST, 'tenantUserID') ? $_POST['tenantUserID']: null;
    $tenantUserID = trim($tenantUserID);
    $guestUserID = filter_has_var(INPUT_POST, 'guestUserID') ? $_POST['guestUserID']: null;
    $guestUserID = trim($guestUserID);
    $subject = filter_has_var(INPUT_POST, 'subject') ? $_POST['subject']: null;
    $subject = trim($subject);
    $message = filter_has_var(INPUT_POST, 'message') ? $_POST['message']: null;
    $message = trim($message);
    
    if($adminUserID !== 'NULL' && $landlordUserID == 'NULL' && $tenantUserID == 'NULL' && $guestUserID == 'NULL')//If $userID is an admin and nothing else
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT accountID FROM Admin WHERE accountID='$adminUserID'";
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
    else if($landlordUserID !== 'NULL' && $adminUserID == 'NULL' && $tenantUserID == 'NULL' && $guestUserID == 'NULL')//If $userID is an admin and nothing else
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT accountID FROM Admin WHERE accountID='$landlordUserID'";
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
    else if($tenantUserID !== 'NULL' && $adminUserID == 'NULL' && $landlordUserID == 'NULL' && $guestUserID == 'NULL')//If $userID is an admin and nothing else
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT accountID FROM Admin WHERE accountID='$tenantUserID'";
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
    else if($guestUserID !== 'NULL' && $adminUserID == 'NULL' && $landlordUserID == 'NULL' && $tenantUserID == 'NULL')//If $userID is an admin and nothing else
    {
        require_once("functions.php");
        $dbConn = getConnection();
        
        $querySQL = "SELECT accountID FROM Admin WHERE accountID='$guestUserID'";
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
    else
    {
        header('Location: mailSystem.php');
    }

    ?>

</body>
</html>