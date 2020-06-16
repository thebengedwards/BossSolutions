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
    <meta id='monthsRemaining' monthsRemaining=<?php echo 12 - date('m');?> >
    <?php
    if (date('d') < 15)
    {
        $utilNextPay = date('m', strtotime('-1 month'));
    }
    else
    {
        $utilNextPay = date('m');
    }
    $utilMonthsLeft = 12 - $utilNextPay;
    echo "<meta id='monthsUtilRemaining' monthsRemaining='{$utilMonthsLeft}'>"
    ?>
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
        turnButtGreen('tenantBills');

        $yearT = date('Y');

        $utilityBool = filter_has_var(INPUT_GET, 'utility')
            ? $_GET['utility'] : false;

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
        $totalUtil = $totRent->bills * 12;

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
        $SQLquery = "SELECT SUM(amount) AS rentPaid
                 FROM tenantTransaction
                 WHERE tenantTransaction.tenant_id_fk =:tenantID AND tenantTransaction.type = 0 AND year(tenantTransaction.date) =:yearT
                         ";
        $stmt = $dbConn->prepare($SQLquery);
        $stmt->execute(array(':tenantID' => $_SESSION['userID'], ':yearT' => $yearT));
        $rentPaid = $stmt->fetchObject();
        $rentPaidT = round($rentPaid->rentPaid, 2);

        $outRent = $totalRent - $rentPaidT;
        $outUtil = $totalUtil - $utilPaidT ;

        $month = date('m');
        $monthlyRent = round(($outRent/(12-$month)), 2);

        if (date('d') < 15)
        {
            $utilNextPay = date('m', strtotime('-1 month'));
        }
        else
        {
            $utilNextPay = date('m');
        }
        $monthlyUtil = round(($outUtil/(12-$utilNextPay)), 2);

        echo "<div class='trancTenantPaymentMainHolder'>
              <form id='tenantPaymentForm' action='tenantPaymentProcess.php' method='get' > 
                <table style='border-collapse: collapse; background-color: transparent'>
                    <tr style='background-color: transparent'>
                        <td id='rentLeftTop' style='width: 30%; border: solid 1px black; border-right: none; border-bottom: none; border-radius: 35px'>
                        
                        </td>
                        <td style='width: 40%; border: none; padding-bottom: 0px'>
                            <div id='rentRightTop' class='trancPaymentPageRentHolder'><h1>Pay Rent</h1></div>
                            <div style='width: 60px; margin: -57px auto;'
                            <span style='text-align: center'>
                                <label class='trancTenantPaymentSwitch'>";
        if ($utilityBool == true)
        {
            echo "<input name='rentUtilCheckbox' id='rentUtilCheckbox' oninput='checkboxChange()' type='checkbox' checked=\"checked\">";
        }
        else
        {
            echo "<input name='rentUtilCheckbox' id='rentUtilCheckbox' oninput='checkboxChange()' type='checkbox'>";
        }
        echo "                      <span class='trancTenantPaymentSlider'></span>
                                    </label>
                            </span>
                            </div>
                            <div id='utilLeftTop' class='trancPaymentUtilRentHolder'><h1>Pay Utilities</h1></div>

                        </td>
                        <td id='utilRightTop' style='width: 30%; border: solid 1px black; border-left: none; border-bottom: none;'>
                        
                        </td>
                        
                    </tr>
                    <tr style='background-color: transparent; border: none;'>
                        <td id='rentLeftSecond' style='width: 30%; border: solid 1px black; border-top: none; border-bottom: none;'>
                        
                        </td>
                        <td style='width: 40%; border: none;'>
                            
                        </td>
                        <td id='utilRightSecond' style='width: 30%; border: solid 1px black; border-top: none;border-bottom: none;'>
                        
                        </td>
                        
                    </tr>
                    <tr style='background-color: transparent; border: none;'>
                        <td id='rentLeftThird' style='width: 30%; border: solid 1px black; border-top: none;'>
                            <table style='background-color: transparent; border-collapse: collapse;'>
                                <tr style='background-color: transparent;'>
                                    <td style='border: none'>Total Rent Due (yearly):</td>
                                    <td style='border: none' id='totRentCont' totRent='{$totalRent}'>£{$totalRent}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>Outstanding Rent:</td>
                                    <td id='outRent' outRent='{$outRent}' style='border: none'>£{$outRent}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>Outstanding After Payment:</td>
                                    <td style='border: none' id='outRentCont' style='opacity: 1.0'>£{$outRent}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>New Rent Due Monthly:</td>
                                    <td style='border: none' id='monthlyRentCont'>£{$monthlyRent}</td>
                                </tr>
                            </table>
                        </td>
                        <td style='width: 40%; border: none;'>
                        <div style='width 100%; text-align: center'>
                            <span style='text-align: center'><label style='font-family: \"microsoft sans serif\"; font-size: 20px; margin-right: 5px;'>Amount: £</label><input name='amountInput' oninput='amountInputChange()' id='amountInput' class='trancTenantAmountInput' type='number' min='0.00' step='0.01' placeholder='0.00'></span>
                            <br><span><input id='formSubmit' type='submit' value='Pay Rent' class='trancTenantFormSubmit'></span>
                        </div>
                        </td>
                        <td id='utilRightThird' style='width: 30%; border: solid 1px black; border-top: none;'>
                            <table style='background-color: transparent; border-collapse: collapse;'>
                                <tr style='background-color: transparent;'>
                                    <td style='border: none'>Total Utility Cost (yearly):</td>
                                    <td style='border: none'>£{$totalUtil}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>Outstanding:</td>
                                    <td id='outUtil' outUtil='{$outUtil}' style='border: none'>£{$outUtil}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>Outstanding After Payment:</td>
                                    <td style='border: none' id='outUtilCont' style='opacity: 1.0'>£{$outUtil}</td>
                                </tr>
                                <tr style='background-color: transparent; border: none;'>
                                    <td style='border: none'>New Rent Due Monthly:</td>
                                    <td style='border: none' id='monthlyUtilCont'>£{$monthlyUtil}</td>
                                </tr>
                            </table>
                        </td>
                        
                    </tr>
                </table>
              </form>
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

<script type="text/javascript">
    window.onload = function() {
        checkboxChange();
    }

    function checkboxChange()
    {
        const checkbox = document.getElementById('rentUtilCheckbox');
        var ticked = checkbox.checked;

        const rentLeftTop = document.getElementById('rentLeftTop');
        const rentRightTop = document.getElementById('rentRightTop');
        const rentLeftSecond = document.getElementById('rentLeftSecond');
        const rentLeftThird = document.getElementById('rentLeftThird');
        const utilLeftTop = document.getElementById('utilLeftTop');
        const utilRightTop = document.getElementById('utilRightTop');
        const utilRightSecond = document.getElementById('utilRightSecond');
        const utilRightThird = document.getElementById('utilRightThird');
        const formSubmitButt = document.getElementById('formSubmit');

        if (ticked == true)
        {
            //Turn Utility Lines Thick Green
            utilLeftTop.style.marginTop = '-3px';
            utilLeftTop.style.borderLeftColor = '#1ed760';
            utilLeftTop.style.borderLeftWidth = '2px';
            utilLeftTop.style.borderTopColor = '#1ed760';
            utilLeftTop.style.borderTopWidth = '2px';
            utilLeftTop.style.borderBottomColor = '#1ed760';
            utilLeftTop.style.borderBottomWidth = '2px';

            utilRightTop.style.borderColor = '#1ed760';
            utilRightTop.style.borderWidth = '2px';
            utilRightSecond.style.borderColor = '#1ed760';
            utilRightSecond.style.borderWidth = '2px';
            utilRightThird.style.borderColor = '#1ed760';
            utilRightThird.style.borderWidth = '2px';

            //Change form submit value
            formSubmitButt.setAttribute('value', 'Pay Utilities');

            //Turn Rent Lines Thin Black
            rentLeftTop.style.borderLeftColor = '#000000';
            rentLeftTop.style.borderLeftWidth = '1px';
            rentLeftTop.style.borderTopColor = '#000000';
            rentLeftTop.style.borderTopWidth = '1px';

            rentRightTop.style.borderRightColor = '#000000';
            rentRightTop.style.borderRightWidth = '1px';
            rentRightTop.style.borderTopColor = '#000000';
            rentRightTop.style.borderTopWidth = '1px';
            rentRightTop.style.borderBottomColor = '#000000';
            rentRightTop.style.borderBottomWidth = '1px';
            rentRightTop.style.marginTop = '-8px';

            rentLeftSecond.style.borderColor = '#000000';
            rentLeftSecond.style.borderWidth = '1px';
            rentLeftThird.style.borderColor = '#000000';
            rentLeftThird.style.borderWidth = '1px';

        }
        else
        {
            //Turn Rent Lines Thick Green
            rentLeftTop.style.borderLeftColor = '#1ed760';
            rentLeftTop.style.borderLeftWidth = '2px';
            rentLeftTop.style.borderTopColor = '#1ed760';
            rentLeftTop.style.borderTopWidth = '2px';

            rentRightTop.style.borderRightColor = '#1ed760';
            rentRightTop.style.borderRightWidth = '2px';
            rentRightTop.style.borderTopColor = '#1ed760';
            rentRightTop.style.borderTopWidth = '2px';
            rentRightTop.style.borderBottomColor = '#1ed760';
            rentRightTop.style.borderBottomWidth = '2px';
            rentRightTop.style.marginTop = '-9px';

            rentLeftSecond.style.borderColor = '#1ed760';
            rentLeftSecond.style.borderWidth = '2px';
            rentLeftThird.style.borderColor = '#1ed760';
            rentLeftThird.style.borderWidth = '2px';

            //Change form submit value
            formSubmitButt.setAttribute('value', 'Pay Rent');

            //Turn Utility Lines Thin Black
            utilLeftTop.style.marginTop = '-3px';
            utilLeftTop.style.borderLeftColor = '#000000';
            utilLeftTop.style.borderLeftWidth = '1px';
            utilLeftTop.style.borderTopColor = '#000000';
            utilLeftTop.style.borderTopWidth = '1px';
            utilLeftTop.style.borderBottomColor = '#000000';
            utilLeftTop.style.borderBottomWidth = '1px';

            utilRightTop.style.borderColor = '#000000';
            utilRightTop.style.borderWidth = '1px';
            utilRightSecond.style.borderColor = '#000000';
            utilRightSecond.style.borderWidth = '1px';
            utilRightThird.style.borderColor = '#000000';
            utilRightThird.style.borderWidth = '1px';

        }
        amountInputChange();
    }

    function amountInputChange()
    {
        const checkbox = document.getElementById('rentUtilCheckbox');
        var ticked = checkbox.checked;

        if (ticked == true)
        {
            const amountInputed = document.getElementById('amountInput').value;
            const outUtil = document.getElementById('outUtil').getAttribute('outUtil');
            var outUtilAfterPayment = outUtil - amountInputed;
            const outUtilAfterPaymentCont = document.getElementById('outUtilCont');
            outUtilAfterPayment = outUtilAfterPayment.toFixed(2);
            outUtilAfterPaymentCont.innerHTML = '£'+outUtilAfterPayment;
            const monthsRemaining = document.getElementById('monthsUtilRemaining').getAttribute('monthsRemaining');
            var monthlyUtil = outUtilAfterPayment/monthsRemaining;
            monthlyUtil = monthlyUtil.toFixed(2);
            const monthlyUtilCont = document.getElementById('monthlyUtilCont');
            monthlyUtilCont.innerHTML = '£'+monthlyUtil;

            const outRent = document.getElementById('outRent').getAttribute('outRent');
            const outRentAfterPaymentCont = document.getElementById('outRentCont');
            outRentAfterPaymentCont.innerHTML = '£'+outRent;
            const monthsRentRemaining = document.getElementById('monthsRemaining').getAttribute('monthsRemaining');
            var monthlyRent2 = outRent/monthsRentRemaining;
            monthlyRent2 = monthlyRent2.toFixed(2);
            const monthlyRentCont = document.getElementById('monthlyRentCont');
            monthlyRentCont.innerHTML = '£'+monthlyRent2;

            if (outUtilAfterPayment < 0)
            {
                outUtilAfterPaymentCont.style.backgroundColor = 'rgba(255, 0, 0, 1.0)';
                outUtilAfterPaymentCont.style.opacity = '1.0';
                const amountInputCont = document.getElementById('amountInput');
                amountInputCont.style.backgroundColor = 'rgba(255, 0, 0, 1.0)';
                amountInputCont.style.opacity = '1.0';
                var opacity = 1.0;
                outUtilAfterPaymentCont.innerHTML = '£0';
                amountInputCont.value = outUtil;
                monthlyUtilCont.innerHTML = '£0';

                var timer = setInterval(function(){
                    opacity = opacity - 0.02;
                    var rgb = 'rgba(255, 0, 0, '.concat(opacity.toString(),')');
                    outUtilAfterPaymentCont.style.backgroundColor = rgb;
                    amountInputCont.style.backgroundColor = rgb;
                    if (opacity <= 0)
                    {
                        clearInterval(timer);
                        outUtilAfterPaymentCont.style.backgroundColor = 'white';
                        amountInputCont.style.backgroundColor = 'white';
                    }
                },20)

            }
        }
        else
        {

            const amountInputed = document.getElementById('amountInput').value;
            const outRent = document.getElementById('outRent').getAttribute('outRent');
            var outRentAfterPayment = outRent - amountInputed;
            const outRentAfterPaymentCont = document.getElementById('outRentCont');
            outRentAfterPayment = outRentAfterPayment.toFixed(2);
            outRentAfterPaymentCont.innerHTML = '£'+outRentAfterPayment;
            const monthsRemaining = document.getElementById('monthsRemaining').getAttribute('monthsRemaining');
            var monthlyRent = outRentAfterPayment/monthsRemaining;
            monthlyRent = monthlyRent.toFixed(2);
            const monthlyRentCont = document.getElementById('monthlyRentCont');
            monthlyRentCont.innerHTML = '£'+monthlyRent;

            const outUtil = document.getElementById('outUtil').getAttribute('outUtil');
            const outUtilAfterPaymentCont = document.getElementById('outUtilCont');
            outUtilAfterPaymentCont.innerHTML = '£'+outUtil;
            const monthsUtilRemaining = document.getElementById('monthsUtilRemaining').getAttribute('monthsRemaining');
            var monthlyUtil2 = outUtil/monthsUtilRemaining;
            monthlyUtil2 = monthlyUtil2.toFixed(2);
            const monthlyUtilCont = document.getElementById('monthlyUtilCont');
            monthlyUtilCont.innerHTML = '£'+monthlyUtil2;

            if (outRentAfterPayment < 0)
            {
                console.log('yess');
                outRentAfterPaymentCont.style.backgroundColor = 'rgba(255, 0, 0, 1.0)';
                outRentAfterPaymentCont.style.opacity = '1.0';
                const amountInputCont = document.getElementById('amountInput');
                amountInputCont.style.backgroundColor = 'rgba(255, 0, 0, 1.0)';
                amountInputCont.style.opacity = '1.0';
                var opacity = 1.0;
                outRentAfterPaymentCont.innerHTML = '£0';
                amountInputCont.value = outRent;
                monthlyRentCont.innerHTML = '£0';

                var timer = setInterval(function(){
                    opacity = opacity - 0.02;
                    var rgb = 'rgba(255, 0, 0, '.concat(opacity.toString(),')');
                    outRentAfterPaymentCont.style.backgroundColor = rgb;
                    amountInputCont.style.backgroundColor = rgb;
                    if (opacity <= 0)
                    {
                        clearInterval(timer);
                        outRentAfterPaymentCont.style.backgroundColor = 'white';
                        amountInputCont.style.backgroundColor = 'white';
                    }
                },20)

            }
        }
    }



</script>


</body>
</html>

