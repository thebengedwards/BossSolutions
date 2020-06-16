<?php
ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Edit Property</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <link rel='icon' type='image/x-icon' href='favicon.ico' />
</head>
<body>
<?php

require_once("functions.php");
checkLogin('index');
buildNav();
buildFooter();
turnButtGreen('propertyMan');
?>
<div class="propertyEdit">
<h1> Property Management </h1>
<h2> Edit Property</h2>

<?php
$propertyID = filter_has_var(INPUT_GET, 'propertyID') ? $_GET['propertyID'] : null;
// if the Property selected doesnt match a PropertyID from the database display error message.
if (empty($propertyID)) {
    echo "<p>Please <a href='PropertyAdmin.php'>choose</a> a property.</p>\n";
} else {
    try {
        require_once("functions.php");
        $dbConn = getConnection();
// retrieving the information from phpmyadmin that is required to edit the information in the database.
        $sqlQuery = "SELECT propertyID, address1, address2, postcode, rent, bills, capacity, description
                 FROM Property
                 WHERE propertyID = $propertyID";

        $queryResult = $dbConn->query($sqlQuery);
        $rowObj = $queryResult->fetchObject();
        echo"<div class='PropertyEditor'>";
echo"<div class = 'PropertyEditorForm'>
		<form id='updateProperty' action='PropertyUpdate.php' method='get'>
			<p>Property ID: <input type='text' name='propertyID' value='$propertyID' READONLY /></p>
			<p>Address 1 <input type='text' name='address1' size='50' value='{$rowObj->address1}' /></p>
			<p>Address 2 <input type='text' name='address2' size='50' value='{$rowObj->address2}' /></p>
			<p>Postcode <input type='text' name='postcode' size='50' value='{$rowObj->postcode}' /></p>
			<p>Rent: <input type='number' name='rent' size='50' value='{$rowObj->rent}' /></p>
			<p>Bills: <input type='number' name='bills' size='50' value='{$rowObj->bills}' /></p>
            <p>Capacity: <input type='number' name='capacity' size='2' value='{$rowObj->capacity}' /></p>
            <label>Description of Property</label><br>
            <textarea rows='8' cols='60' name= 'description' id= 'description'>{$rowObj->description} </textarea> <br>
            
            ";
        echo "<p><input type='submit' name='submit' value='Update Property'></p>
          </form></div>";

    } catch (Exception $e) {
        echo "<p>Property details not found: " . $e->getMessage() . "</p>";
    }
}
echo"</div></div>";

?>
<h1> Property Image Management</h1>
<div class = InsertPropertyImage>
<form method='post' action='' enctype='multipart/form-data'>
    <Label> Type of Room: </Label>
    <select name="roomType">
    <option value = "Bathroom"> Bathroom </option>
    <option value = "Bedroom"> Bedroom </option>
    <option value = "Living Room"> Living Room</option>
    <option value = "Dining Room"> Dining Room </option>
    <option value = "Kitchen"> Kitchen </option>
    <option value = "Ensuite"> Ensuite </option>
    <option value = "Garage"> Garage </option>
    <option value = "Garden"> Garden </option>
    <option value = "Utility Room"> Utility Room </option>
    <option value = "Outside - Front View"> Outside - Front View </option>
    <option value = "Outside - Rear View"> Outside - Rear View </option>
    <option value = "Entrance Hallway"> Entrance Hallway </option>
    <option value = "Landing"> Landing </option>
    </select><br>
    <input type='file' name='files[]' multiple />
    <input type='submit' value='Submit' name='submit'/>
</form>

<?php
if (isset($_POST['submit'])) {
    $roomType = filter_has_var(INPUT_POST, 'roomType') ? $_POST['roomType'] : null;
    // Count total files
    $countfiles = count($_FILES['files']['name']);

    // Prepared statement
    $query = "INSERT INTO PropertyImages (type, name, image, propertyID) VALUES('$roomType', ?, ?, '$propertyID')";
    $statement = $dbConn->prepare($query);
    // Loop all files

    for ($i = 0; $i < $countfiles; $i++) {
        // File name
        $filename = $_FILES['files']['name'][$i];

        // Location
        $target_file = '/home/unn_w17004394/public_html/BossSolutions/PropertyImages/' . $filename;

        // file extension
        $file_extension = pathinfo($target_file, PATHINFO_EXTENSION);
        $file_extension = strtolower($file_extension);

        // Valid image extension
        $valid_extension = array("png", "jpeg", "jpg", "jfif", "Chrome HTML Document");

        if (in_array($file_extension, $valid_extension)) {

            // Upload file
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_file)) {

                // Execute query
                $statement->execute(array($filename, $target_file));

            }
        }

    }
}

?>
</div>
    <?php
    $removeImageQuery = "SELECT imageID, name, image, propertyID, type
                 FROM PropertyImages
                 WHERE propertyID = $propertyID";
    $removeResult = $dbConn->query($removeImageQuery);
    echo"<div class = deleteImagesEditPage>";
    while ($rowObj = $removeResult->fetchObject()) {
        echo "<div class='RemoveImageList'>
                   <span class='viewPropertyImage'><a href='../BossSolutions/PropertyImages/{$rowObj->name}' target='_blank'>View Image</a></span>
				   <span class='imageID' style='display:none'>{$rowObj->imageID}</span>
				   <span class='name' >Name: {$rowObj->name}</span>
				   <span class='type'>Type: {$rowObj->type}</span>
				   <span class='removePropertyImage'><a href='RemovePropertyImage.php?imageID={$rowObj->imageID}'>Remove Image</a></span><br>
				   </div>
				   ";
    }

    ?>
</div>
</body>
</html>