<?php
    $MYSQL_USERNAME="";
    $MYSQL_PASSWORD="";
    $MYSQL_HOST="";
    $MYSQL_DB="";
    if (empty($MYSQL_USERNAME) || empty($MYSQL_PASSWORD) || empty($MYSQL_HOST) || empty($MYSQL_DB)) {
        die("mysqli_connect form hasn't filled!");
    }
    $conn = mysqli_connect($MYSQL_HOST,$MYSQL_USERNAME,$MYSQL_PASSWORD);
    if (!$conn) {
        die(mysqli_connect_errno() . ':' . mysqli_connect_error());
    }
    mysqli_select_db($conn,$MYSQL_DB);
?>
