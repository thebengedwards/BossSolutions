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
    <?php
    require_once("functions.php");
    buildPage();

    $rentGraphYear = filter_has_var(INPUT_GET, 'rentGraphYear')
        ? $_GET['rentGraphYear'] : null;

    if(($rentGraphYear < 2010)||($rentGraphYear > 2100))
    {
        $rentGraphYear = date('Y');
    }

    $propertySelected = filter_has_var(INPUT_GET, 'propertySelected')
        ? $_GET['propertySelected'] : null;

    $tenantSelected = filter_has_var(INPUT_GET, 'tenantSelected')
        ? $_GET['tenantSelected'] : null;

    $rentGraphYear = (int)$rentGraphYear;

    $dbConn = getConnection();
    $SQLquery = "SELECT Property.propertyID
                     FROM Landlord
                     LEFT JOIN Property ON Landlord.landlordID = Property.landlordID
                     WHERE Landlord.accountID = :landlordID AND Property.propertyID = :propID
                         ";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected));
    if (!($stmt->fetchObject()))
    {
        $dbConn = getConnection();
        $SQLquery = "SELECT Property.propertyID
                         FROM Landlord
                         LEFT JOIN Property ON Landlord.landlordID = Property.landlordID
                         WHERE Landlord.accountID = :landlordID 
                         ";
        $stmt2 = $dbConn->prepare($SQLquery);
        $stmt2->execute(array(':landlordID' => $_SESSION['userID']));
        $propertyTest = $stmt2->fetchObject();
        if (!($propertyTest->propertyID == ''))
        {
            $propertySelected = 'all';
        }
        else
        {
            $propertySelected = 'null';
        }

    }

    if (!($propertySelected == 'null'))
    {
        if ($propertySelected == 'all')
        {
            buildAllPropertiesMeta();
        }
        else if (($propertySelected > 0)&&($tenantSelected == 'all'))
        {
            buildAllTenantsMeta($propertySelected);
        }
        else
        {
            buildIndiTenantsMeta($propertySelected, $tenantSelected);
        }

    }

    function buildAllPropertiesMeta()
    {
        $dbConn = getConnection();
        $SQLquery = "SELECT amount, date
                     FROM tenantTransaction
                     LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                     LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                     WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1
                     ORDER BY date ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID']));

        while ($rowObj = $stmt->fetchObject())
        {
            $amount = $rowObj->amount;
            $newDate = date("d-m-Y", strtotime($rowObj->date));
            echo "<meta class='rentDataPoint' amount='{$amount}' date='{$newDate}'/>";
        }

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                 ORDER BY date ASC";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID']));
        $totRent = $stmt->fetchObject();
        $totalRent = $totRent->rent * 12;

        $outRent = $totalRent - $rentPaidT;

        if (date('d') < 15)
        {
            $month = date('m', strtotime('-1 month'));
        }
        else
        {
            $month = date('m');
        }

        $monthlyRent = round(($outRent/(12-$month)), 2);

        if ($monthlyRent > 0)
        {
            $j = date('d') - 15;
            if ($j < 0)
            {
                $day = '+'.($j*-1);
            }
            else
            {
                $day = '-'.$j;
            }

            for ($i=0; $i<(12-$month);$i++)
            {
                $newDate = date('d-m-Y', strtotime($day.' day +'.$i.'month'));
                echo "<meta class='upcomingRentDataPoint' amount='{$monthlyRent}' date='{$newDate}'/>";
            }
        }
    }

    function buildAllTenantsMeta($propertySelected)
    {
        $dbConn = getConnection();
        $SQLquery = "SELECT amount, date
                     FROM tenantTransaction
                     LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                     LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                     WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND Tenant.propertyID = :propID
                     ORDER BY date ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected));

        while ($rowObj = $stmt->fetchObject())
        {
            $amount = $rowObj->amount;
            $newDate = date("d-m-Y", strtotime($rowObj->date));
            echo "<meta class='rentDataPoint' amount='{$amount}' date='{$newDate}'/>";
        }

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT AND Tenant.PropertyID = :propID
                 ORDER BY date ASC";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020, ':propID' => $propertySelected));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND Tenant.PropertyID = :propID
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected));
        $totRent = $stmt->fetchObject();
        $totalRent = $totRent->rent * 12;

        $outRent = $totalRent - $rentPaidT;

        if (date('d') < 15)
        {
            $month = date('m', strtotime('-1 month'));
        }
        else
        {
           $month = date('m');
        }
        $monthlyRent = round(($outRent/(12-$month)), 2);

        if ($monthlyRent > 0)
        {
            $j = date('d') - 15;
            if ($j < 0)
            {
                $day = '+'.($j*-1);
            }
            else
            {
                $day = '-'.$j;
            }

            for ($i=0; $i<(12-$month);$i++)
            {
                $newDate = date('d-m-Y', strtotime($day.' day +'.$i.'month'));
                echo "<meta class='upcomingRentDataPoint' amount='{$monthlyRent}' date='{$newDate}'/>";
            }
        }
    }

    function buildIndiTenantsMeta($propertySelected, $tenantSelected)
    {
        $dbConn = getConnection();
        $SQLquery = "SELECT amount, date
                     FROM tenantTransaction
                     LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                     LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                     WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND Tenant.propertyID = :propID
                     AND tenantTransaction.tenant_id_fk =:tenID
                     ORDER BY date ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected, ':tenID' => $tenantSelected));

        while ($rowObj = $stmt->fetchObject())
        {
            $amount = $rowObj->amount;
            $newDate = date("d-m-Y", strtotime($rowObj->date));
            echo "<meta class='rentDataPoint' amount='{$amount}' date='{$newDate}'/>";
        }

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT 
                 AND Tenant.PropertyID = :propID AND tenantTransaction.tenant_id_fk =:tenID
                 ORDER BY date ASC";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020, ':propID' => $propertySelected, ':tenID' => $tenantSelected));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND Tenant.PropertyID = :propID AND Tenant.accountID =:tenID
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected, ':tenID' => $tenantSelected));
        $totRent = $stmt->fetchObject();
        $totalRent = $totRent->rent * 12;

        $outRent = $totalRent - $rentPaidT;

        if (date('d') < 15)
        {
            $month = date('m', strtotime('-1 month'));
        }
        else
        {
            $month = date('m');
        }
        $monthlyRent = round(($outRent/(12-$month)), 2);

        if ($monthlyRent > 0)
        {
            $j = date('d') - 15;
            if ($j < 0)
            {
                $day = '+'.($j*-1);
            }
            else
            {
                $day = '-'.$j;
            }

            for ($i=0; $i<(12-$month);$i++)
            {
                $newDate = date('d-m-Y', strtotime($day.' day +'.$i.'month'));
                echo "<meta class='upcomingRentDataPoint' amount='{$monthlyRent}' date='{$newDate}'/>";
            }
        }
    }


    echo "<meta id='rentGraphYearMeta' year='$rentGraphYear'/>";


    ?>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <title>Home Page</title>
</head>
<body>

<?php

require_once("functions.php");
checkLogin('landlordUtilities');

buildNav();

if ($_SESSION['loggedIn'] == true)
{
    if (checkPermission('Landlord') == true)
    {
        turnButtGreen('landlordBills');

        echo "
                <table class='trancTenantRentInTable'>
                    <tr>
                        <td style='width: 50%;'>
                            <table>
                            <tr>
                            <td style='width: 25%'><h1 style='font-family: \"microsoft sans serif\"; margin-top: 0; margin-bottom: 0; font-size: 20px'>Utility Income</h1></td>
                            <td style='width: 55%'>
                                
                                    <select class='trancLandlordRentSelector' id='propertySelected' name='propertySelected' onchange='reloadFunc(2020)'>";

        $dbConn = getConnection();
        $SQLquery = "SELECT Property.propertyID
                     FROM Landlord
                     LEFT JOIN Property ON Landlord.landlordID = Property.landlordID
                     WHERE Landlord.accountID = :landlordID
                     ORDER BY Property.postcode ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':landlordID' => $_SESSION['userID']));
        if ($stmt->fetchObject()->propertyID == '')
        {
            echo "<option value='all'>No Properties</option> ";
        }
        else
        {
            echo "<option value='all'>All Properties</option> ";

            $dbConn = getConnection();
            $SQLquery = "SELECT Property.postcode, Property.propertyID, Property.address1, Property.address2
                     FROM Landlord
                     LEFT JOIN Property ON Landlord.landlordID = Property.landlordID
                     WHERE Landlord.accountID = :landlordID
                     ORDER BY Property.postcode ASC
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':landlordID' => $_SESSION['userID']));

            while ($rowObj = $stmt->fetchObject())
            {
                if ($rowObj->propertyID == $propertySelected)
                {
                    echo "<option selected value='$rowObj->propertyID'>{$rowObj->postcode}, {$rowObj->address1} {$rowObj->address2}</option>";
                }
                else
                {
                    echo "<option value='$rowObj->propertyID'>{$rowObj->postcode}, {$rowObj->address1} {$rowObj->address2}</option>";
                }

            }
        }

        echo "                           </select>
                            </td>
                            <td style='width: 25%'>

                                    <select class='trancLandlordRentSelector' id='tenantSelected' name='tenantSelected' onchange='reloadFunc($rentGraphYear)'>
                                      ";
        echo "<option value='all'>All Tenants</option> ";

        $dbConn = getConnection();
        $SQLquery = "SELECT Account.firstName, Account.lastName, Account.accountID
                     FROM Account
                     LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                     WHERE Tenant.PropertyID = :propID
                     ORDER BY Account.lastName ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':propID' => $propertySelected));

        while ($rowObj = $stmt->fetchObject())
        {

            if ($rowObj->accountID == $tenantSelected)
            {
                echo "<option selected value='$rowObj->accountID'>{$rowObj->firstName} {$rowObj->lastName}</option>";
            }
            else
            {
                echo "<option value='$rowObj->accountID'>{$rowObj->firstName} {$rowObj->lastName}</option>";
            }
        }


        echo "                      </select>
                            </td>
                            </tr>
                            </table>
        
        
        
                        </td>";

        echo "          <td style='width: 25%;'>";

        if ($propertySelected > 0)
        {
            echo "<a href='PropertyEdit.php?propertyID={$propertySelected}'><div class='tranTenantRentPaymentButt'>Manage Property</div></a>";
        }
        else
        {
            echo "<a href='PropertyLandlord.php'><div class='tranTenantRentPaymentButt'>Manage Properties</div></a>";
        }

        echo "          </td>
                        <td style='width: 25%;'>
                            <a href='landlordBills.php'><div class='tranTenantRentReturnButt'>Return To Dashboard</div></a>
                        </td>
                    </tr>";

        if (!($propertySelected == 'null'))
        {
            if ($propertySelected == 'all')
            {
                buildAllProperties($rentGraphYear, $propertySelected);
            }
            else if (($propertySelected > 0)&&($tenantSelected == 'all'))
            {
                buildAllTenant($rentGraphYear, $propertySelected);
            }
            else
            {
                buildIndiTenant($rentGraphYear, $propertySelected, $tenantSelected);
            }

        }
        echo  "</table>";
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

function buildAllProperties($rentGraphYear, $propertySelected)
{
    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020));
    $rentPaid = $stmt->fetchObject();
    $rentPaidT = round($rentPaid->rentPaid, 2);

    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID
                         ";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID']));
    $totRent = $stmt->fetchObject();
    $totalRent = $totRent->rent * 12;

    $outRent = $totalRent - $rentPaidT;

    if (date('d') < 15)
    {
        $month = date('m', strtotime('-1 month'));
    }
    else
    {
        $month = date('m');
    }
    $monthlyRent = round(($outRent/(12-$month)), 2);
    $monthlyRent = number_format($monthlyRent, 2);
    $totalRent = number_format($totalRent, 2);
    $rentPaidT = number_format($rentPaidT, 2);
    $outRent = number_format($outRent, 2);

    $nextMonth = date('F',strtotime('first day of +1 month'));
    $nextPayment = "1st ".$nextMonth;

    echo "               <tr style='background-color: transparent; border: 1px solid #333333;'>
                        <td style='width: 50%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>Next Payment Due:</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>{$nextPayment}</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>£{$monthlyRent}</p></td>
                    </tr>
                    <tr>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income (Yearly): £{$totalRent}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income Received: £{$rentPaidT}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Outstanding Utility Income: £{$outRent}</p></td>
                    </tr>
                </table>
        <table class='trancTenantRentTable'>
        <tr>
            <td>
                <div class='trancTenantRentYearHolder'>
                    <div class='trancYearLeftArrow' onclick='reloadPageWithDateBack()'><p style='font-size: 30px; margin-top: 0px'>←</p></div>
                    <div class='trancYearMain'><p style='font-size: 20px; margin-top: 0px'>{$rentGraphYear}</p></div>
                    <div class='trancYearRightArrow' onclick='reloadPageWithDateFor()'><p style='font-size: 30px; margin-top: 0px'>→</p></div>
                </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='rentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
            <td>
            <div class='trancTenantRentYearHolder'>
            </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='upcomingRentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
        </tr>
        <tr>
            <td style='width: 50%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";
    $dbConn = getConnection();
    $SQLquery = "SELECT amount, date 
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => $rentGraphYear, ':yearT' => $rentGraphYear));

    while ($rowObj = $stmt->fetchObject())
    {
        $date = date('jS F Y', strtotime($rowObj->date));
        $amount = number_format($rowObj->amount,2);
        echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
    }

    echo "          </table>
                </div>
            </td>
            <td style='width: 100%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";

    if ($monthlyRent > 0)
    {
        $j = date('d') - 15;
        echo $j;
        if ($j < 0)
        {
            $day = '+'.($j*-1);
        }
        else
        {
            $day = '-'.$j;
        }

        for ($i=0; $i<(12-$month);$i++)
        {
            $date = date('jS F Y', strtotime($day.' day +'.$i.'month'));
            //$amount = number_format($monthlyRent, 2);
            $amount = $monthlyRent;
            echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
        }
    }

    echo "          </table>
                </div>
            </td>
        </tr>";
}

function buildAllTenant($rentGraphYear, $propertySelected)
{
    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT AND Tenant.propertyID = :propID
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020, ':propID' => $propertySelected));
    $rentPaid = $stmt->fetchObject();
    $rentPaidT = round($rentPaid->rentPaid, 2);

    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND Tenant.propertyID = :propID
                         ";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected));
    $totRent = $stmt->fetchObject();
    $totalRent = $totRent->rent * 12;

    $outRent = $totalRent - $rentPaidT;

    if (date('d') < 15)
    {
        $month = date('m', strtotime('-1 month'));
    }
    else
    {
        $month = date('m');
    }
    $monthlyRent = round(($outRent/(12-$month)), 2);
    $monthlyRent = number_format($monthlyRent, 2);
    $totalRent = number_format($totalRent, 2);
    $rentPaidT = number_format($rentPaidT, 2);
    $outRent = number_format($outRent, 2);

    $nextMonth = date('F',strtotime('first day of +1 month'));
    $nextPayment = "1st ".$nextMonth;

    echo "               <tr style='background-color: transparent; border: 1px solid #333333;'>
                        <td style='width: 50%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>Next Payment Due:</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>{$nextPayment}</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>£{$monthlyRent}</p></td>
                    </tr>
                    <tr>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income (Yearly): £{$totalRent}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income Received: £{$rentPaidT}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Outstanding Utility Income: £{$outRent}</p></td>
                    </tr>
                </table>
        <table class='trancTenantRentTable'>
        <tr>
            <td>
                <div class='trancTenantRentYearHolder'>
                    <div class='trancYearLeftArrow' onclick='reloadPageWithDateBack()'><p style='font-size: 30px; margin-top: 0px'>←</p></div>
                    <div class='trancYearMain'><p style='font-size: 20px; margin-top: 0px'>{$rentGraphYear}</p></div>
                    <div class='trancYearRightArrow' onclick='reloadPageWithDateFor()'><p style='font-size: 30px; margin-top: 0px'>→</p></div>
                </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='rentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
            <td>
            <div class='trancTenantRentYearHolder'>
            </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='upcomingRentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
        </tr>
        <tr>
            <td style='width: 50%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";
    $dbConn = getConnection();
    $SQLquery = "SELECT amount, date 
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT AND Tenant.propertyID =:propID
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => $rentGraphYear, ':yearT' => $rentGraphYear, ':propID' => $propertySelected));

    while ($rowObj = $stmt->fetchObject())
    {
        $date = date('jS F Y', strtotime($rowObj->date));
        $amount = number_format($rowObj->amount,2);
        echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
    }

    echo "          </table>
                </div>
            </td>
            <td style='width: 100%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";

    if ($monthlyRent > 0)
    {
        $j = date('d') - 15;
        if ($j < 0)
        {
            $day = '+'.($j*-1);
        }
        else
        {
            $day = '-'.$j;
        }

        for ($i=0; $i<(12-$month);$i++)
        {
            $date = date('jS F Y', strtotime($day.' day +'.$i.'month'));
            $amount = number_format($monthlyRent, 2);
            echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
        }
    }

    echo "          </table>
                </div>
            </td>
        </tr>";
}

function buildIndiTenant($rentGraphYear, $propertySelected, $tenantSelected)
{
    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(tenantTransaction.amount) AS rentPaid
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT 
                 AND Tenant.propertyID = :propID AND tenantTransaction.tenant_id_fk =:tenID
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => 2020, ':propID' => $propertySelected, ':tenID' => $tenantSelected));
    $rentPaid = $stmt->fetchObject();
    $rentPaidT = round($rentPaid->rentPaid, 2);

    $dbConn = getConnection();
    $SQLquery = "SELECT SUM(Property.bills) AS rent
                 FROM Tenant
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND Tenant.propertyID = :propID AND Tenant.accountID =:tenID
                         ";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':propID' => $propertySelected, ':tenID' => $tenantSelected));
    $totRent = $stmt->fetchObject();
    $totalRent = $totRent->rent * 12;

    $outRent = $totalRent - $rentPaidT;

    if (date('d') < 15)
    {
        $month = date('m', strtotime('-1 month'));
    }
    else
    {
        $month = date('m');
    }
    $monthlyRent = round(($outRent/(12-$month)), 2);
    $monthlyRent = number_format($monthlyRent, 2);
    $totalRent = number_format($totalRent, 2);
    $rentPaidT = number_format($rentPaidT, 2);
    $outRent = number_format($outRent, 2);

    $nextMonth = date('F',strtotime('first day of +1 month'));
    $nextPayment = "1st ".$nextMonth;

    echo "               <tr style='background-color: transparent; border: 1px solid #333333;'>
                        <td style='width: 50%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>Next Payment Due:</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>{$nextPayment}</p></td>
                        <td style='width: 25%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>£{$monthlyRent}</p></td>
                    </tr>
                    <tr>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income (Yearly): £{$totalRent}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Utility Income Received: £{$rentPaidT}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Outstanding Utility Income: £{$outRent}</p></td>
                    </tr>
                </table>
        <table class='trancTenantRentTable'>
        <tr>
            <td>
                <div class='trancTenantRentYearHolder'>
                    <div class='trancYearLeftArrow' onclick='reloadPageWithDateBack()'><p style='font-size: 30px; margin-top: 0px'>←</p></div>
                    <div class='trancYearMain'><p style='font-size: 20px; margin-top: 0px'>{$rentGraphYear}</p></div>
                    <div class='trancYearRightArrow' onclick='reloadPageWithDateFor()'><p style='font-size: 30px; margin-top: 0px'>→</p></div>
                </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='rentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
            <td>
            <div class='trancTenantRentYearHolder'>
            </div>
                <div class='trancTenantDashHolderMain'>
                    <canvas id='upcomingRentChart' style='width: 99%; height: 178px;'></canvas>
                </div>
            </td>
        </tr>
        <tr>
            <td style='width: 50%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";
    $dbConn = getConnection();
    $SQLquery = "SELECT amount, date 
                 FROM tenantTransaction
                 LEFT JOIN Account ON tenantTransaction.tenant_id_fk = Account.accountID
                 LEFT JOIN Tenant ON Account.accountID = Tenant.accountID
                 LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                 LEFT JOIN Landlord ON Property.landlordID = Landlord.landlordID
                 WHERE Landlord.accountID = :landlordID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT 
                 AND Tenant.propertyID =:propID AND tenantTransaction.tenant_id_fk =:tenID
                 ORDER BY date ASC";
    $stmt = $dbConn->prepare($SQLquery);
    $stmt->execute(array(':landlordID' => $_SESSION['userID'], ':yearT' => $rentGraphYear, ':yearT' => $rentGraphYear, ':propID' => $propertySelected, ':tenID' => $tenantSelected));

    while ($rowObj = $stmt->fetchObject())
    {
        $date = date('jS F Y', strtotime($rowObj->date));
        $amount = number_format($rowObj->amount,2);
        echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
    }

    echo "          </table>
                </div>
            </td>
            <td style='width: 100%'>
                <div class='trancTenantRentHistoryTableCont'>
                    <table class='trancTenantRentHistoryTable'>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>";

    if ($monthlyRent > 0)
    {
        $j = date('d') - 15;
        if ($j < 0)
        {
            $day = '+'.($j*-1);
        }
        else
        {
            $day = '-'.$j;
        }

        for ($i=0; $i<(12-$month);$i++)
        {
            $date = date('jS F Y', strtotime($day.' day +'.$i.'month'));
            $amount = number_format($monthlyRent, 2);
            echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
        }
    }

    echo "          </table>
                </div>
            </td>
        </tr>";
}

?>

<script type="text/javascript"> //Rent Chart

    var dataPoints = document.getElementsByClassName('rentDataPoint');
    var dataVar = [];
    var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
    var l = dataPoints.length;
    var i = 0;
    for (i = 0; i < l; i++)
    {
        var amount = dataPoints[i].getAttribute('amount');
        var date = dataPoints[i].getAttribute('date');
        var entry = {x: date, y: amount};
        dataVar.push(entry);

    }

    var timeFormat = 'DD/MM/YYYY';

    const lowerDate = '01/01/'.concat(year);
    const upperDate = '31/12/'.concat(year);

    var config = {
        type:    'line',
        data:    {
            datasets: [
                {
                    data: dataVar,
                    fill: false,
                    borderColor: '#1ed760',
                    events: 'click'
                }
            ]
        },
        options: {
            responsive: false,
            title: {
                display: true,
                text: 'Utility Payment History'

            },
            legend: {
                display: false
            },
            scales:     {
                xAxes: [{
                    type:       "time",
                    ticks: {
                        maxTicksLimit: 12,
                        min: lowerDate,
                        max: upperDate
                    },
                    time:       {
                        unit: 'month',
                        format: timeFormat,
                        tooltipFormat: 'll',
                        distribution: 'linear',

                    },
                    scaleLabel: {
                        display:     true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 4,
                        suggestedMin: 0,
                    },
                    scaleLabel: {
                        display:     true,
                        labelString: 'Amount Received £'
                    }
                }]
            }
        }
    };

    //Upcoming Rent Chart

    var upcomingDataPoints = document.getElementsByClassName('upcomingRentDataPoint');
    var upcomingDataVar = [];
    var ul = upcomingDataPoints.length;
    var j = 0;
    for (j = 0; j < ul; j++)
    {
        var upcomingAmount = upcomingDataPoints[j].getAttribute('amount');
        var upcomingDate = upcomingDataPoints[j].getAttribute('date');
        var upcomingEntry = {x: upcomingDate, y: upcomingAmount};
        upcomingDataVar.push(upcomingEntry);
    }

    var upcomingTimeFormat = 'DD/MM/YYYY';

    const upcomingLowerDate = '01/01/2020';
    const upcomingUpperDate = '31/12/2020';

    var upcomingConfig = {
        type:    'line',
        data:    {
            datasets: [
                {
                    data: upcomingDataVar,
                    fill: false,
                    borderColor: '#1ed760',
                    events: 'click'
                }
            ]
        },
        options: {
            responsive: false,
            title: {
                display: true,
                text: 'Upcoming Utility Payments'

            },
            legend: {
                display: false
            },
            scales:     {
                xAxes: [{
                    type:       "time",
                    ticks: {
                        maxTicksLimit: 12,
                        min: upcomingLowerDate,
                        max: upcomingUpperDate
                    },
                    time:       {
                        unit: 'month',
                        format: upcomingTimeFormat,
                        tooltipFormat: 'll',
                        distribution: 'linear'
                    },
                    scaleLabel: {
                        display:     true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 4,
                        suggestedMin: 0
                    },
                    scaleLabel: {
                        display:     true,
                        labelString: 'Amount To Recieve £'
                    }
                }]
            }
        }
    };

    function reloadPageWithDateBack()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);
        year = year - 1;

        reloadFunc(year);
    }

    function reloadPageWithDateFor()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);
        year = year + 1;

        reloadFunc(year);
    }

    window.onload = function () {
        var ctx = document.getElementById("rentChart").getContext("2d");
        window.myLine = new Chart(ctx, config);
        var ctxx = document.getElementById("upcomingRentChart").getContext("2d");
        window.myLine = new Chart(ctxx, upcomingConfig);
    };

    function reloadFunc(graphYear)
    {
        const page = 'landlordUtilities.php?rentGraphYear=';
        const text1 = '&propertySelected=';
        const propertyID = document.getElementById('propertySelected').value;
        const text2 = '&tenantSelected=';
        const tenantID = document.getElementById('tenantSelected').value;
        const holder = page.concat(graphYear, text1, propertyID, text2, tenantID);
        document.location = holder;
    }


</script>

</body>
</html>