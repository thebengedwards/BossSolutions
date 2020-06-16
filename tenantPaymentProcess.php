<?php
/**
 * Created by PhpStorm.
 * User: calumwillis
 * Date: 13/02/2020
 * Time: 17:25
 */

ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <title>Home Page</title>
</head>
<body>

<?php

require_once("functions.php");
buildPage();
checkLogin('tenantPaymentPage');

buildNav();
if ($_SESSION['loggedIn'] == true)
{
    if (checkPermission('Tenant') == true)
    {
        $rentUtilCheckbox = filter_has_var(INPUT_GET, 'rentUtilCheckbox')
            ? $_GET['rentUtilCheckbox'] : 'off';

        $amountInput = filter_has_var(INPUT_GET, 'amountInput')
            ? $_GET['amountInput'] : null;

        $checkboxCheck = array('on', 'off');

        if (((filter_var($amountInput, FILTER_VALIDATE_INT))||(filter_var($amountInput, FILTER_VALIDATE_FLOAT))) && (in_array($rentUtilCheckbox, $checkboxCheck)))
        {
            $amountInput=round($amountInput,2);
            $yearT = date('Y');

            $dbConn = getConnection();
            $SQLquery = "SELECT Property.rent, Property.bills
                     FROM Tenant
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     WHERE Tenant.accountID = :tenantID
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':tenantID' => $_SESSION['userID']));
            $totRent = $stmt->fetchObject();
            $rent = $totRent->rent*12;
            $bills = $totRent->bills*12;

            //Input validation is good
            if ($rentUtilCheckbox == 'off')
            {
                //For rent payment

                $dbConn = getConnection();
                $SQLquery = "SELECT SUM(amount) AS rentPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 0 AND year(tenantTransaction.date) =:yearT
                         ";
                $stmt = $dbConn->prepare($SQLquery);
                $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => $yearT));
                $rentPaid = $stmt->fetchObject();
                $rentPaidT = round($rentPaid->rentPaid, 2);

                if ((($rent - $rentPaidT) - $amountInput) < 0)
                {
                    $amountInput = $rent - $rentPaidT;
                }

                $dbConn = getConnection();
                $SQLinsert = "INSERT INTO tenantTransaction (tenant_id_fk, amount, date, type)
                              VALUES (:userID, :amount, :dateT, 0)";
                $stmt = $dbConn->prepare($SQLinsert);
                $stmt->execute(array(':userID' => $_SESSION['userID'], ':amount' => $amountInput, ':dateT' => date('Y-m-d')));

                echo "<p style='text-align: center'>Thank you! A payment of £{$amountInput} was taken for your rent.</p>
                        <a href='tenantBills.php' style='text-decoration: none'><div class='trancTenantReturnButt'>Return</div></a>";


            }
            else
            {
                //For utility payment
                $dbConn = getConnection();
                $SQLinsert = "INSERT INTO tenantTransaction (tenant_id_fk, amount, date, type)
                              VALUES (:userID, :amount, :dateT, 1)";
                $stmt = $dbConn->prepare($SQLinsert);
                $stmt->execute(array(':userID' => $_SESSION['userID'], ':amount' => $amountInput, ':dateT' => date('Y-m-d')));
            }

        }
        else
        {
            //Input validation is bad
            echo "<p>Unfortunately an error occurred and this payment has not been processed</p>";
        }

    }
    else
    {
        echo "<p style='text-align: center'>You do not have permission to access this page.</p>";
    }
}
else
{
    echo "<p style='text-align: center'>You do not have permission to access this page.</p>";
}

buildFooter();
chartJS();

?>


</body>
</html>
