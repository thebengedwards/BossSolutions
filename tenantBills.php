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
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 0
                 ORDER BY date ASC
                         ";
            $stmt = $dbConn->prepare($SQLquery);
            $stmt->execute(array(':tenantID' => $_SESSION['userID']));

            while ($rowObj = $stmt->fetchObject())
            {
                $newDate = date("d-m-Y", strtotime($rowObj->date));
                echo "<meta class='rentDataPoint' amount='{$rowObj->amount}' date='{$newDate}'/>";
            }

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
                echo "<meta class='rentUDataPoint' amount='{$rowObj->amount}' date='{$newDate}'/>";
            }

            $rentGraphYear = filter_has_var(INPUT_GET, 'rentGraphYear')
                ? $_GET['rentGraphYear'] : null;

            $rentGraphYear = (int)$rentGraphYear;

            if(($rentGraphYear < 2010)||($rentGraphYear > 2100))
            {
                $rentGraphYear = date('Y');
            }

            echo "<meta id='rentGraphYearMeta' year='$rentGraphYear'/>";

            $rentUGraphYear = filter_has_var(INPUT_GET, 'rentUGraphYear')
                ? $_GET['rentUGraphYear'] : null;

            $rentUGraphYear = (int)$rentUGraphYear;

            if(($rentUGraphYear < 2010)||($rentUGraphYear > 2100))
            {
                $rentUGraphYear = date('Y');
            }

            echo "<meta id='rentUGraphYearMeta' yearU=$rentUGraphYear/>";
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
        $yearT = date('Y');

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(amount) AS rentPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 0 AND year(tenantTransaction.date) =:yearT
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => $yearT));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT SUM(amount) AS utilPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 1 AND year(tenantTransaction.date) =:yearT
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => $yearT));
        $utilPaid = $stmt->fetchObject();
        $utilPaidT = round($utilPaid->utilPaid, 2);

        $dbConn = getConnection();
        $SQLquery = "SELECT Property.rent, Property.bills
                     FROM Tenant
                     LEFT JOIN Property ON Property.propertyID = Tenant.PropertyID
                     WHERE Tenant.accountID = :tenantID
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID']));
        $totRent = $stmt->fetchObject();
        $totalRent = $totRent->rent * 12;
        $outRent = $totalRent - $rentPaidT;

        $totalUtil = $totRent->bills * 12;
        $outUtil = $totalUtil - $utilPaidT ;


        $month = date('m');
        $monthlyRent = round(($outRent/(12-$month)), 2);
        $nextMonth = date('M',strtotime('first day of +1 month'));

        if (date('d') < 15)
        {
            $utilDate = date('M');
            $utilNextPay = date('m', strtotime('-1 month'));
        }
        else
        {
            $utilDate = $nextMonth;
            $utilNextPay = date('m');
        }
        $monthlyUtil = round(($outUtil/(12-$utilNextPay)), 2);
        $monthlyRent = number_format($monthlyRent, 2);
        $monthlyUtil = number_format($monthlyUtil, 2);


        $totalDue = $totalRent + $totalUtil;
        $totalPaid = $rentPaidT + $utilPaidT;
        $totalRemain = $totalDue - $totalPaid;

        $totalDue = number_format($totalDue, 2);
        $totalPaid = number_format($totalPaid, 2);
        $totalRemain = number_format($totalRemain, 2);
        $totalRent = number_format($totalRent, 2);
        $rentPaidT = number_format($rentPaidT, 2);
        $outRent = number_format($outRent, 2);
        $totalUtil = number_format($totalUtil, 2);
        $utilPaidT = number_format($utilPaidT, 2);
        $outUtil = number_format($outUtil, 2);


        turnButtGreen('tenantBills');
        echo "<div class='trancTenantMain'>
                <table class='trancTenantHolder'>
                    <tr>
                        <td style='width: 50%;'>
                            <div class='trancTenantDashHolder' onmouseover='changeNameColor(\"trancTName1\")' onmouseout='changeNameColorBack(\"trancTName1\")'>
                                <div class='trancTenantDashHolder2'>
                                    <div class='trancTenantDashName' id='trancTName1'>
                                        <p style='margin-left: 10px; margin-top: 5px;'>Bills Overview</p>
                                    </div>
                                    <div style='width: 100%;'>
                                        <table>
                                            <tr>
                                                <td><p>Upcoming Payments: </p></td>
                                                <td><a href='tenantRent.php'><div class='trancTenantUpcomingHolder'><p>1st {$nextMonth} - £{$monthlyRent}</p></div></a></td>
                                                <td><a href='tenantUtilities.php'><div class='trancTenantUpcomingHolder'><p>15th {$utilDate} - £{$monthlyUtil}</p></div></a></td>
                                            </tr>
                                            <tr>
                                                <td style='height: 100px;'><p>Total Due: £{$totalDue}</p></td>
                                                <td><p>Total Paid: £{$totalPaid}</p></td>
                                                <td><p>Total Remaining: £{$totalRemain}</p></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class='trancTenantDashHolder' onmouseover='changeNameColor(\"trancTName2\")' onmouseout='changeNameColorBack(\"trancTName2\")'>
                                    <div class='trancTenantDashHolder2'>
                                        <a href='tenantRent.php?2020'>
                                            <div class='trancTenantDashName' id='trancTName2'>
                                                    <p style='margin-left: 10px; margin-top: 5px;'>My Rent</p><p id='transNameArrows2' style='margin-left: 120px; margin-top: -33px;'> >></p>
                                            </div>
                                        </a>
                                        <div class='trancTenantYearHolder'>
                                            <div class='trancYearLeftArrow' onclick='reloadPageWithDateBack()'><p style='font-size: 30px; margin-top: 0px'>←</p></div>
                                            <div class='trancYearMain'><p style='font-size: 20px; margin-top: 0px'>{$rentGraphYear}</p></div>
                                            <div class='trancYearRightArrow' onclick='reloadPageWithDateFor()'><p style='font-size: 30px; margin-top: 0px'>→</p></div>
                                        </div>
                                        <table style='background-color: transparent'>
                                            <tr>
                                                <td style='width: 26%; border-right: solid 1px #333333'>
                                                        <p style='font-size: 10px'>Next Rent Due:</p>
                                                        <p style='font-size: 10px'>1st {$nextMonth} - £{$monthlyRent}</p><hr style='border: solid 0.5px #333333'>
                                                        <p style='font-size: 10px'>Total Rent Due: £{$totalRent}</p>
                                                        <p style='font-size: 10px'>Rent Paid: £{$rentPaidT}</p>
                                                        <p style='font-size: 10px'>Outstanding Rent: £{$outRent}</p>  
                                                        <p style='font-size: 10px'>Rent Due Monthly: £{$monthlyRent}</p>                                               
                                                        <p style='font-size: 10px'>Payment Date Each Month: 1st</p>                                               
                                                </td>
                                                <td>
                                                    <div class='trancTenantDashHolderMain'>
                                                        <canvas id='rentChart' style='width: 99%; height: 178px;'></canvas>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                             </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class='trancTenantDashHolder' onmouseover='changeNameColor(\"trancTName3\")' onmouseout='changeNameColorBack(\"trancTName3\")'>
                                    <div class='trancTenantDashHolder2'>
                                        <a href='tenantUtilities.php'>
                                            <div class='trancTenantDashName' id='trancTName3'>
                                                <p style='margin-left: 10px; margin-top: 5px;'>My Utilities</p><p id='transNameArrows2' style='margin-left: 120px; margin-top: -33px;'> >></p>
                                            </div>
                                        </a>
                                        <div class='trancTenantYearHolder'>
                                            <div class='trancYearLeftArrow' onclick='reloadUPageWithDateBack()'><p style='font-size: 30px; margin-top: 0px'>←</p></div>
                                            <div class='trancYearMain'><p style='font-size: 20px; margin-top: 0px'>{$rentUGraphYear}</p></div>
                                            <div class='trancYearRightArrow' onclick='reloadUPageWithDateFor()'><p style='font-size: 30px; margin-top: 0px'>→</p></div>
                                        </div>
                                        <table style='background-color: transparent'>
                                            <tr>
                                                <td style='width: 26%; border-right: solid 1px #333333'>
                                                        <p style='font-size: 10px'>Next Payment Due:</p>
                                                        <p style='font-size: 10px'>15th {$utilDate} - £{$monthlyUtil}</p><hr style='border: solid 0.5px #333333'>
                                                        <p style='font-size: 10px'>Total Utility Cost: £{$totalUtil}</p>
                                                        <p style='font-size: 10px'>Paid: £{$utilPaidT}</p>
                                                        <p style='font-size: 10px'>Outstanding: £{$outUtil}</p>  
                                                        <p style='font-size: 10px'>Due Monthly: £{$monthlyUtil}</p>                                               
                                                        <p style='font-size: 10px'>Payment Date Each Month: 15th</p>                                               
                                                </td>
                                                <td>
                                                    <div class='trancTenantDashHolderMain'>
                                                        <canvas id='rentUChart' style='width: 99%; height: 178px;'></canvas>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                             </div>
                        </td>
                        <td>
                            <div class='trancTenantDashHolder' onmouseover='changeNameColor(\"trancTName4\")' onmouseout='changeNameColorBack(\"trancTName4\")'>
                                <a href='tenantPaymentPage.php'><div class='trancTenantDashHolder2'>
                                    <div class='trancTenantDashName' id='trancTName4'>
                                        <p style='margin-left: 10px; margin-top: 5px;'>Payment</p><p id='transNameArrows4' style='margin-left: 120px; margin-top: -33px;'> >></p>
                                    </div>
                                    <div style='margin-left: 10px; margin-right: 10px; '>
                                        <p style='margin-left: 20px; margin-right: 20px; margin-top: 50px; text-align: center'>We make it easy for you to budget by allowing you to pay your rent and bills as early as you like. Pay securely through our payment portal here.</p>
                                    </div>
                                    <div style='margin-left: 20px; margin-right: 20px; border-bottom: black solid 1px; box-shadow: 0px 5px 8px #333333'>
                                    </div>
                                </div></a>
                        </td>
                    </tr>
                </table> 
              </div>";
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
                text: 'Rent Payment History'

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

    //Utility Chart

    var dataPoints = document.getElementsByClassName('rentUDataPoint');
    var dataVar = [];
    var year = document.getElementById('rentUGraphYearMeta').getAttribute('yearU');
    var l = dataPoints.length;
    for (i = 0; i < l; i++)
    {
        var amount = dataPoints[i].getAttribute('amount');
        var date = dataPoints[i].getAttribute('date');
        var entry = {x: date, y: amount};
        dataVar.push(entry);
    }

    var timeFormat = 'DD/MM/YYYY';

    const lowerDate2 = '01/01/'.concat(year);
    const upperDate2 = '31/12/'.concat(year);

    var config2 = {
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
                        min: lowerDate2,
                        max: upperDate2
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

    window.onload = function () {
        var ctxx       = document.getElementById('rentUChart').getContext('2d');
        window.myLine = new Chart(ctxx, config2);
        var ctx       = document.getElementById("rentChart").getContext("2d");
        window.myLine = new Chart(ctx, config);
    };


</script>



<script type="text/javascript">

    function changeNameColor(idToChange)
    {
        const element = document.getElementById(idToChange);

        element.style.backgroundColor = '#1ed760';
        element.style.borderColor = '#1ed760';
    }

    function changeNameColorBack(idToChange)
    {
        const element = document.getElementById(idToChange);

        element.style.backgroundColor = '#333333';
        element.style.borderColor = '#333333';

    }

    function reloadPageWithDateBack()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);
        year = year - 1;

        var yearU = document.getElementById('rentUGraphYearMeta').getAttribute('yearU');
        yearU = parseInt(yearU);

        const page = 'tenantBills.php?rentGraphYear=';
        const text = '&rentUGraphYear=';
        const holder = page.concat(year, text, yearU);
        document.location = holder;
    }

    function reloadPageWithDateFor()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);
        year = year + 1;

        var yearU = document.getElementById('rentUGraphYearMeta').getAttribute('yearU');
        yearU = parseInt(yearU);

        const page = 'tenantBills.php?rentGraphYear=';
        const text = '&rentUGraphYear=';
        const holder = page.concat(year, text, yearU);
        document.location = holder;
    }

    function reloadUPageWithDateBack()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);

        var yearU = document.getElementById('rentUGraphYearMeta').getAttribute('yearU');
        yearU = parseInt(yearU);
        yearU = yearU - 1;

        const page = 'tenantBills.php?rentGraphYear=';
        const text = '&rentUGraphYear=';
        const holder = page.concat(year, text, yearU);
        document.location = holder;
    }

    function reloadUPageWithDateFor()
    {
        var year = document.getElementById('rentGraphYearMeta').getAttribute('year');
        year = parseInt(year);

        var yearU = document.getElementById('rentUGraphYearMeta').getAttribute('yearU');
        yearU = parseInt(yearU);
        yearU = yearU + 1;

        const page = 'tenantBills.php?rentGraphYear=';
        const text = '&rentUGraphYear=';
        const holder = page.concat(year, text, yearU);
        document.location = holder;
    }

</script>

</body>
</html>

