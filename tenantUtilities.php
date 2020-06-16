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

    if ($_SESSION['loggedIn'] == true)
    {
        if (checkPermission('Tenant') == true)
        {
            $dbConn = getConnection();
            $SQLquery = "SELECT amount, date
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 1
                 ORDER BY date ASC
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':tenantID' => $_SESSION['userID']));

            while ($rowObj = $stmt->fetchObject())
            {
                $newDate = date("d-m-Y", strtotime($rowObj->date));
                echo "<meta class='rentDataPoint' amount='{$rowObj->amount}' date='{$newDate}'/>";
            }

            $rentGraphYear = filter_has_var(INPUT_GET, 'rentGraphYear')
                ? $_GET['rentGraphYear'] : null;

            $rentGraphYear = (int)$rentGraphYear;

            if(($rentGraphYear < 2010)||($rentGraphYear > 2100)||($rentGraphYear == null))
            {
                $rentGraphYear = date('Y');
            }

            echo "<meta id='rentGraphYearMeta' year='$rentGraphYear'/>";

            $dbConn = getConnection();
            $SQLquery = "SELECT SUM(amount) AS rentPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => 2020));
            $rentPaid = $stmt->fetchObject();
            $rentPaidT = round($rentPaid->rentPaid, 2);

            $dbConn = getConnection();
            $SQLquery = "SELECT Property.bills
                     FROM Tenant
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     WHERE Tenant.accountID = :tenantID
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':tenantID' => $_SESSION['userID']));
            $totRent = $stmt->fetchObject();
            $totalRent = $totRent->bills * 12;

            $outRent = $totalRent - $rentPaidT;

            if (date('d') < 15)
            {
                $month = date('m', strtotime('-1 month'));
            }
            else
            {
                $month = date('m');
            }

            $monthlyUtil = round(($outRent/(12-$month)), 2);

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
                echo "<meta class='upcomingRentDataPoint' amount='{$monthlyUtil}' date='{$newDate}'/>";
            }
        }
    }

    ?>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <title>Home Page</title>
</head>
<body>

<?php

require_once("functions.php");
checkLogin('tenantBills');

buildNav();

if ($_SESSION['loggedIn'] == true)
{
    if (checkPermission('Tenant') == true)
    {
        turnButtGreen('tenantBills');

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(amount) AS rentPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => 2020));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT Property.bills
                     FROM Tenant
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     WHERE Tenant.accountID = :tenantID
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID']));
        $totRent = $stmt->fetchObject();
        $totalRent = $totRent->bills * 12;

        $outRent = $totalRent - $rentPaidT;

        if (date('d') < 15)
        {
            $utilNextPay = date('F');
            $month = date('m', strtotime('-1 month'));
        }
        else
        {
            $utilNextPay = date('F',strtotime('+1 month'));
            $month = date('m');
        }
        $monthlyUtil = round(($outRent/(12-$month)), 2);
        $nextPayment = "15th ".$utilNextPay;

        $totalRent = number_format($totalRent, 2);
        $rentPaidT = number_format($rentPaidT, 2);
        $outRent = number_format($outRent, 2);
        $monthlyUtil = number_format($monthlyUtil, 2);



        echo "
                <table class='trancTenantRentInTable'>
                    <tr>
                        <td style='width: 30%;'><h1 style='font-family: \"microsoft sans serif\"; margin-top: 0; margin-bottom: 0; font-size: 20px'>My Utilities</h1></td>
                        <td style='width: 30%;'>
                            <a href='tenantPaymentPage.php?utility=true'><div class='tranTenantRentPaymentButt'>Make Utilities Payment</div></a>
                        </td>
                        <td style='width: 30%;'>
                            <a href='tenantBills.php'><div class='tranTenantRentReturnButt'>Return To Dashboard</div></a>
                        </td>
                    </tr>
                    <tr style='background-color: transparent; border: 1px solid #333333;'>
                        <td style='width: 30%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>Next Payment Due:</p></td>
                        <td style='width: 30%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>{$nextPayment}</p></td>
                        <td style='width: 30%;'><p style='font-weight: bold; margin-top: 0; margin-bottom: 0;'>£{$monthlyUtil}</p></td>
                    </tr>
                    <tr>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Rent (Yearly): £{$totalRent}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Rent Paid: £{$rentPaidT}</p></td>
                        <td><p style='margin-top: 0; margin-bottom: 0;'>Total Outstanding Rent: £{$outRent}</p></td>
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
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                 ORDER BY tenantTransaction.date ASC
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => $rentGraphYear));

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
            $amount = number_format($monthlyUtil, 2);
            echo "<tr>
                    <td>{$date}</td>
                    <td>£{$amount}</td>
                  </tr>";
        }

        echo "          </table>
                </div>
            </td>
        </tr>
        </table>   
        ";
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
                        labelString: 'Amount Paid £'
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
                text: 'Upcoming Rent Payments'

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
                        labelString: 'Amount To Pay £'
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

        const page = 'tenantUtilities.php?rentGraphYear=';
        const holder = page.concat(year);
        document.location = holder;
    }

    function reloadPageWithDateFor()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);
        year = year + 1;

        const page = 'tenantUtilities.php?rentGraphYear=';
        const holder = page.concat(year);
        document.location = holder;
    }

    window.onload = function () {
        var ctx = document.getElementById("rentChart").getContext("2d");
        window.myLine = new Chart(ctx, config);
        var ctxx = document.getElementById("upcomingRentChart").getContext("2d");
        window.myLine = new Chart(ctxx, upcomingConfig);
    };
</script>

</body>
</html>