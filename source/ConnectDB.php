<?php
    $MYSQL_USERNAME="";
    $MYSQL_PASSWORD="";
    $MYSQL_HOST="";
    $MYSQL_DB="";
    $conn = mysqli_connect($MYSQL_HOST,$MYSQL_USERNAME,$MYSQL_PASSWORD);
    if (!$conn) {
        die('Could not connect: ' . mysql_error());
    }
    mysqli_select_db($conn,$MYSQL_DB);
?>
