<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}

if ($_SESSION['user_role'] !== 'teacher') {
    http_response_code(403);
    include("_err403.php");
    exit();
}

$conn = mysqli_connect($MYSQL_HOST, $MYSQL_USERNAME, $MYSQL_PASSWORD);
if (!$conn) {
    die(mysqli_connect_errno() . ':' . mysqli_connect_error());
}
mysqli_select_db($conn, $MYSQL_DB);

$get_tbl = isset($_GET['tbl']) ? $_GET['tbl'] : '';
$delete_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;

if ($get_tbl === '' || $delete_id === 0) {
    http_response_code(404);
    include("_err404.php");
    exit();
}

switch ($get_tbl) {
    case 'submits':
        $tbl_name = '`submits`';
        break;
    case 'challenges':
        $tbl_name = '`challenges`';
        break;
    case 'assignments':
        $tbl_name = '`assignments`';
        break;
    case 'users':
        $tbl_name = '`users`';
        break;
    default:
        http_response_code(404);
        include("_err404.php");
        exit();
}

// select all linked foreign key
$sql = "SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    REFERENCED_TABLE_SCHEMA = 'chall01'
    AND REFERENCED_TABLE_NAME = ?;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $get_tbl);
$stmt->execute();
$list_linked = $stmt->get_result();

// delete id linked foreign key
if ($list_linked->num_rows > 0) {
    while ($row = $list_linked->fetch_assoc()) {
        $sql = "DELETE FROM `{$row['TABLE_NAME']}` WHERE `{$row['COLUMN_NAME']}` = ?;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        if (!$stmt->execute()) {
            die('Delete failed ' . $row['TABLE_NAME'] . '.' . $row['COLUMN_NAME'] . ' = ' . $delete_id);
        }
    }
}

// delete id
$sql = "DELETE FROM {$tbl_name} WHERE `id` = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $delete_id);
$stmt->execute();

$conn->close();
header('Location:' . $_SERVER['HTTP_REFERER']);
exit;
?>