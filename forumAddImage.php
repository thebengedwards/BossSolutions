<?php

ini_set("session.save_path", "/home/unn_w17004394/sessionData");
session_start();
require_once("functions.php");
?>



<?php

require_once("functions.php");
checkLogin('index');

buildNav();

buildFooter();

?>


<?php

if ($_SESSION['loggedIn'] == true) {

    $checkAdmin = checkPermission('Admin');
    $checkGuest = checkPermission('Guest');
    $checkTenant = checkPermission('Tenant');

    if ($checkAdmin == true || $checkGuest == true || $checkTenant == true) {


            if ($_SERVER['REQUEST_METHOD'] === 'POST') {


                try {

                    $dbConn = getConnection();
                $countfiles = count($_FILES['files']['name']);
                $thread_id = $_POST["thread_id"];
                $filename = $_POST["files[]"];

                $query = "INSERT INTO forum_images (name, image, propertyID) VALUES(?, ?, '$thread_id')";
                $statement = $dbConn->prepare($query);

                for ($i = 0; $i < $countfiles; $i++) {

                    $filename = $_FILES['files']['name'][$i];

                    $target_file = 'forumImages' . $filename;

                    $file_extensions = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file_extensions = strtolower($file_extensions);

                    $extension = array("png", "jpeg", "jpg", "jfif", "Chrome HTML Document");

                    if (in_array($file_extensions, $extension)) {


                        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_file)) {

                            $statement->execute(array($filename, $target_file));

                        }
                    }

                }
            } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                $dbConn = null;

        }
    }
    else {
        header("Location: index.php");
    }
}
else {
    header("Location: index.php");
}

?>

