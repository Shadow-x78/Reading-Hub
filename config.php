<?php
define('DB_SERVER', 'Databasse_URL');
define('DB_USERNAME', 'Databasse_Username');
define('DB_PASSWORD', 'Databasse_Password');
define('DB_NAME', 'Databasse_Name');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>