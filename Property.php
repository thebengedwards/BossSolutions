<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Property</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>
<div class="wrapper">
    <?php
    require_once("functions.php");
    checkLogin('index');
    buildNav();
    turnButtGreen('index');
    ?>
    <h1> Available Properties</h1>
    <?php
    try {
    require_once("functions.php");
    $dbConn = getConnection();
    $session = $_SESSION['username'];

    $sqlSession = "SELECT Account.accountID, Tenant.ID, PropertyID
                 FROM Account
                 JOIN Tenant
                 ON Tenant.accountID = Account.accountID
                 WHERE username = '$session'";
    $stmt = $dbConn->query($sqlSession);
    $stmt->execute(array(':username' => $session));
    $user = $stmt->fetchObject();

    @$accountID = $user->accountID;
    $propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;
    //$propertyID";
    $sqlPropertyImages = "SELECT name, type FROM PropertyImages WHERE propertyID = '$propertyID'";
    $imageCount = 1;
    echo " <div class='PropertyImageContainer'>";
    foreach ($dbConn->query($sqlPropertyImages) as $row) {
        echo "<div class = 'mySlides'>";
        echo "<div class = 'numbertext'> $imageCount </div>";
        echo "<div class = 'propertyImage'>";
        echo "<img class ='myPropertyView' src = PropertyImages/$row[name]  style='width:100%'>
                </div> </div>";
        $imageCount++;
    }
    ?>

    <!-- Next and previous buttons -->
    <a class='prev' onclick='plusSlides(-1)'>&#10094;</a>
    <a class='next' onclick='plusSlides(1)'>&#10095;</a>

    <!-- Image text -->
    <div class='propertyCaption'>
        <p id='caption'></p>
    </div>


    <?php
    $imageCount = 1;
    echo "<div class='propertyRow'>";
    foreach ($dbConn->query($sqlPropertyImages) as $row) {
        echo "<div class = 'propertyColumn'>";
        echo "<img class = 'propertySlideshow cursor' src = PropertyImages/$row[name] onclick='currentSlide($imageCount)' alt='$row[type]'>";
        echo "</div>";
        $imageCount++;
    }
    echo "</div>";
    echo "</div>";
    ?>

    <script>
        var slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("propertySlideshow");
            var captionText = document.getElementById("caption");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
            captionText.innerHTML = dots[slideIndex - 1].alt;
        }
    </script>

    <div class = 'propertyDetails'>
        <?php
        // retrieving the information from phpmyadmin that is required for php
        $sqlProperty = "SELECT Property.propertyID, address1, address2, postcode, rent, bills, capacity
                 FROM Property
                 WHERE Property.propertyID = $propertyID";

        $queryResult = $dbConn->query($sqlProperty);


        $sqlLandlord = $dbConn->prepare("SELECT landlordID FROM Property WHERE propertyID = $propertyID");
        $sqlLandlord->execute();
        $landlordID = $sqlLandlord->fetchColumn();

        $sqlAccountID = $dbConn->prepare("SELECT accountID FROM Landlord WHERE landlordID = $landlordID");
        $sqlAccountID->execute();
        $landlordAccountID = $sqlAccountID->fetchColumn();


        $sqlLandlordAccount = "SELECT username, firstName, lastName, email, phoneNumber
                 FROM Account
                 JOIN Landlord
                 ON Landlord.accountID = Account.accountID
                 WHERE Account.accountID = $landlordAccountID";
        $landlordAccountResult = $dbConn->query($sqlLandlordAccount);

        while ($rowObj = $queryResult->fetchObject()) {
            echo "<div class='availablePropertyDetailList' >
				   <span class='Address 1'>Address: {$rowObj->address1}, {$rowObj->address2}</span><br>
				   <span class='postcode'>Postcode: {$rowObj->postcode}</span><br>
				   <span class='Rent'>Rent: £{$rowObj->rent}</span><br>
				   <span class='Bills'>Bills: £{$rowObj->bills}</span><br>
				   <span class='capacity'>Property capacity: {$rowObj->capacity}</span><br>
				   <span class='propertyID' style='display:none' value='{$rowObj->propertyID}'></span><br>
				   </div>";
        }
        echo "<div class='availableLandlordDetails' >";
        echo"Landlord: ";
        while ($rowObj = $landlordAccountResult->fetchObject()) {
            echo "<span class='username' style='display:none'> Username: {$rowObj->username}</span><br>
                  <span class='firstName'>First Name: {$rowObj->firstName}</span><br>
                  <span class='lastName'>Surname: {$rowObj->lastName}</span><br>
                  <span class='email'>Email Address: {$rowObj->email}</span><br>
				  <span class='phoneNumber'>Phone Number: {$rowObj->phoneNumber}</span><br>
				   </div><br>";
        }
        } catch (Exception $e) {
            echo "<p>Query failed: " . $e->getMessage() . "</p>\n";
            echo "<p>Go to Property List by clicking <a href='PropertyAdmin.php'>here</a>.</p>\n";
        }
        echo"</div>";
        buildFooter();
        ?>
    </div>
</body>
</html>