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

function downloadFile($filePath, $fileName)
{
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath);
        header('location:blank.php');
        exit;
    } else {
        echo "File not found: " . $fileName;
    }
}

$get_tbl = isset($_GET['tbl']) ? $_GET['tbl'] : '';
$file_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;

if ($get_tbl === '' || $file_id === 0) {
    http_response_code(404);
    include("_err404.php");
    exit();
}

switch ($get_tbl) {
    case 'submits':
        $tbl_name = '`submits`';
        break;
    default:
        http_response_code(404);
        include("_err404.php");
        exit();
}

$sql = "SELECT * FROM {$tbl_name} WHERE `id` = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();
$obj = $result->fetch_assoc();

downloadFile(($_SERVER['DOCUMENT_ROOT'] . $obj['files']), $obj['ogfilename']);
$conn->close();
?>