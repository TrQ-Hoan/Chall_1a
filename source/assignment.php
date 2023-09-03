<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}

$conn = mysqli_connect($MYSQL_HOST, $MYSQL_USERNAME, $MYSQL_PASSWORD);
if (!$conn) {
    die(mysqli_connect_errno() . ':' . mysqli_connect_error());
}
mysqli_select_db($conn, $MYSQL_DB);

function uploadFile(array $mfile, $fileDest)
{
    $fileName = $mfile['name'];
    $fileTmpName = $mfile['tmp_name'];
    $fileSize = $mfile['size'];
    $fileError = $mfile['error'];
    $fileType = $mfile['type'];
    $fileDestination = $_SERVER['DOCUMENT_ROOT'] . $fileDest;
    $fileActualExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('txt', 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'zip', 'rar');
    if (!in_array($fileActualExt, $allowed)) {
        echo "<script>alert('File type not allowed: " . $fileActualExt . "');</script>";
        return false;
    }
    if ($fileError !== 0) {
        echo "<script>alert('File upload failed');</script>";
        return false;
    }
    if ($fileSize > 2097152) {
        echo "<script>alert('File size too large');</script>";
        return false;
    }
    move_uploaded_file($fileTmpName, $fileDestination);
    return true;
}

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
        exit;
    } else {
        echo "File not found.";
    }
}

$cur_assignment_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cur_assignment_id === 0) {
    http_response_code(404);
    include("_err404.php");
    exit();
}

$sql = "SELECT `assignments`.*,`users`.`username` AS `teacherusername`,`users`.`fullname` AS `teacherfullname`
    FROM `assignments` JOIN `users` ON `assignments`.`teacherid` = `users`.`id` WHERE `assignments`.`id` = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cur_assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$cur_assignment_obj = $result->fetch_assoc();
if (!isset($cur_assignment_obj)) {
    http_response_code(404);
    include("_err404.php");
    exit();
}
$filePath = $_SERVER['DOCUMENT_ROOT'] . $cur_assignment_obj['files'];

$sql = "SELECT * FROM `submits` WHERE `assignmentid` = ? AND `usersubmitid` = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cur_assignment_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$submit_info = $result->fetch_assoc();

if (isset($_POST['submit'])) {
    $mfile = $_FILES['submitFile'];
    $submit_file_name = md5($_SESSION['user_name'] . '_' . $_SESSION['user_id'] . '-' . $cur_assignment_id);
    $fileDestination = '/archive/submits/' . $submit_file_name;
    $ret_upload = uploadFile($mfile, $fileDestination);
    if (isset($mfile['name']) && $ret_upload) {
        if (isset($submit_info['id'])) {
            $sql = "UPDATE `submits` SET `ogfilename` = ? WHERE `assignmentid` = ? AND `usersubmitid` = ? AND `files` = ?;";
        } else {
            $sql = "INSERT INTO `submits` (`ogfilename`, `assignmentid`, `usersubmitid`, `files`) VALUES (?, ?, ?, ?);";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siis", $mfile['name'], $cur_assignment_id, $_SESSION['user_id'], $fileDestination);
        $stmt->execute();
        header("Refresh:0");
        exit;
    }
}

if (isset($_POST['download']) && $_POST['download'] === 'Download') {
    $fileName = basename($filePath);
    downloadFile($filePath, $fileName);
}
?>

<!DOCTYPE html>
<html lang='en' data-bs-theme="auto">

<head>
    <title>QLSV - Assignment</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIxMDUuODEgLTE4LjExMSA0OCA0OCIgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzPgogICAgPGZpbHRlciBpZD0iZWRpdGluZy1nbG93aW5nIiB4PSItMTAwJSIgeT0iLTEwMCUiIHdpZHRoPSIzMDAlIiBoZWlnaHQ9IjMwMCUiPgogICAgICA8ZmVHYXVzc2lhbkJsdXIgaW49IlNvdXJjZUdyYXBoaWMiIHJlc3VsdD0iYmx1ciIgc3RkRGV2aWF0aW9uPSI2Ii8+CiAgICAgIDxmZU1lcmdlPgogICAgICAgIDxmZU1lcmdlTm9kZSBpbj0iYmx1ciIvPgogICAgICAgIDxmZU1lcmdlTm9kZSBpbj0iU291cmNlR3JhcGhpYyIvPgogICAgICA8L2ZlTWVyZ2U+CiAgICA8L2ZpbHRlcj4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZWRpdGluZy1nbG93aW5nLWdyYWRpZW50IiB4MT0iMCIgeDI9IjEiIHkxPSIwLjUiIHkyPSIwLjUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiM2N2ZmNDMiLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjOTBmZmZmIi8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICA8cmVjdCB4PSI5My42ODMiIHk9Ii0yNC41NjUiIHdpZHRoPSI2OS45NzQiIGhlaWdodD0iNjUuMzcyIiBzdHlsZT0iZmlsbDogcmdiKDM3LCAzNywgMzcpOyIvPgogIDxnIGZpbHRlcj0idXJsKCNlZGl0aW5nLWdsb3dpbmcpIiB0cmFuc2Zvcm09Im1hdHJpeCgxLCAwLCAwLCAxLCAtMTIwLjk5MzU0NTUzMjIyNjU2LCAtNzAuMzY4NDYxNjA4ODg2NzIpIj4KICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIzNi43NzUwMDc3MjQ3NjE5NiwgODcuMTE5OTk5ODg1NTU5MDgpIj4KICAgICAgPHBhdGggZD0iTTIuNjUtMTEuNzZMMi42NS0xMS43NlEyLjY1LTE1LjMzIDUuNTQtMTYuNjZMNS41NC0xNi42Nkw1LjU0LTE2LjY2UTYuOTQtMTcuMzEgOC44Ny0xNy4zMUw4Ljg3LTE3LjMxTDguODctMTcuMzFRMTAuMTMtMTcuMzEgMTEuMzEtMTdMMTEuMzEtMTdMMTEuMzEtMTdRMTIuNDgtMTYuNjkgMTMuMjktMTYuMTJMMTMuMjktMTYuMTJMMTMuMjktMjMuODBMMTUuMjAtMjMuODBMMTUuMjAgMEwxMy4zMyAwLjE3TDEzLjMzIDAuMTdRMTEuMTIgMC40MSA5Ljg2IDAuNDFMOS44NiAwLjQxTDkuODYgMC40MVE4LjYwIDAuNDEgNy41MCAwLjE3TDcuNTAgMC4xN0w3LjUwIDAuMTdRNi4zOS0wLjA3IDUuMzAtMC42MUw1LjMwLTAuNjFMNS4zMC0wLjYxUTQuMDgtMS4yMiAzLjM3LTIuNDFMMy4zNy0yLjQxTDMuMzctMi40MVEyLjY1LTMuNjAgMi42NS01LjM0TDIuNjUtNS4zNEwyLjY1LTExLjc2Wk0xMy4yOS0xNC40NUwxMy4yOS0xNC40NVExMi4zOC0xNC45NiAxMS4xOS0xNS4yOEwxMS4xOS0xNS4yOEwxMS4xOS0xNS4yOFExMC4wMC0xNS42MSA4Ljg0LTE1LjYxTDguODQtMTUuNjFMOC44NC0xNS42MVE2Ljk0LTE1LjYxIDUuNzMtMTQuNjdMNS43My0xNC42N0w1LjczLTE0LjY3UTQuNTItMTMuNzQgNC41Mi0xMS42Nkw0LjUyLTExLjY2TDQuNTItNS4zMEw0LjUyLTUuMzBRNC41Mi0xLjMzIDEwLjAwLTEuMzNMMTAuMDAtMS4zM0wxMC4wMC0xLjMzUTExLjI5LTEuMzMgMTMuMjktMS42M0wxMy4yOS0xLjYzTDEzLjI5LTE0LjQ1Wk0yMS40MiAwLjQ0TDIxLjQyIDAuNDRRMTkuNTItMC4xMCAxOC42Ny0xLjI5TDE4LjY3LTEuMjlMMTguNjctMS4yOVExNy44Mi0yLjQ4IDE3LjgyLTQuNDlMMTcuODItNC40OUwxNy44Mi0yMy44MEwxOS43Mi0yMy44MEwxOS43Mi0xNy4wM0wyMy44MC0xNy4wM0wyMi45OC0xNS4yM0wxOS43Mi0xNS4yM0wxOS43Mi00LjQ5TDE5LjcyLTQuNDlRMTkuNzItMS43NyAyMS45Ni0xLjAyTDIxLjk2LTEuMDJMMjEuNDIgMC40NFoiIGZpbGw9InVybCgjZWRpdGluZy1nbG93aW5nLWdyYWRpZW50KSIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+#CqQkestbA4">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/color-modes.js"></script>
</head>

<body>
    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" style="margin-left: 3%;" href="/">QLSV</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/assignments.php">Assignments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/challenges.php">Challenges</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav" style="margin-right: 3%;">
                    <form action="/logout.php" class="form-outline me-2 mb-1 mt-1">
                        <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                    <a href="/account.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-sm btn-primary mb-1 mt-1"><i class="bi bi-person-square"></i></a>
                </ul>
            </div>
        </div>
    </nav>
    <!-- main page -->
    <?php if ($_GET['view'] === 'stu' || $_SESSION['user_role'] === 'student') { ?>
        <section class="w-100 p-4 d-flex justify-content-center pb-4">
            <div style="width: 26rem;">
                <h2 class="form-outline mb-4">Assignment</h2>
                <!-- Title -->
                <div class="small text-secondary"><sub>Title</sub></div>
                <b><?php echo $cur_assignment_obj['title']; ?></b>
                <!-- Teacher -->
                <div class="small text-secondary"><sub>Teacher</sub></div>
                <b><?php echo $cur_assignment_obj['teacherfullname']; ?></b>
                <!-- Description -->
                <div class="small text-secondary"><sub>Description</sub></div>
                <b><?php echo $cur_assignment_obj['description']; ?></b>
                <!-- Assignment -->
                <div class="small text-secondary"><sub>Assignment File</sub></div>

                <form method="POST">
                    <div>
                        <input class="btn btn-link" type="submit" name="download" value="Download">
                    </div>
                </form>
                <?php if (isset($user_error) && !empty($user_error)) { ?>
                    <!-- Error message -->
                    <div class="text-center"><a style="color:red"><?php echo $user_error; ?></a></div>
                <?php } ?>
                <hr>

                <form method="POST" enctype="multipart/form-data">
                    <?php if (isset($submit_info)) { ?>
                        <div class="small text-secondary mb-1">Last submited at <?php echo $submit_info['lastupdate']; ?></div>
                    <?php } ?>
                    <div class="row">
                        <div class="col">
                            <input class="form-control" type="file" name="submitFile">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-block mb-3" name="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    <?php } elseif ($_SESSION['user_role'] === 'teacher') { ?>
        <div class="container mt-2">
            <h2 class="mb-4">Assignment</h2>
            <?php
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : "%%";
            $lowsearch = strtolower($search);

            $sql = "SELECT `submits`.*, `users`.`username` AS `username`, `users`.`fullname` AS `userfullname` 
            FROM `submits` JOIN `users` ON `submits`.`usersubmitid` = `users`.`id` 
            WHERE `assignmentid` = ? AND (
                        LOWER(`users`.`username`) LIKE ? OR 
                        LOWER(`users`.`fullname`) LIKE ? OR 
                        LOWER(`ogfilename`) LIKE ? OR 
                        LOWER(`files`) LIKE ? OR 
                        LOWER(`lastupdate`) LIKE ?
                    ) LIMIT $limit OFFSET $offset;";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $cur_assignment_id, $lowsearch, $lowsearch, $lowsearch, $lowsearch, $lowsearch);
            $stmt->execute();
            $tbl_exercise = $stmt->get_result();

            // Calculate total pages for pagination
            $sql = "SELECT COUNT(*) AS total FROM `submits` JOIN `users` ON `submits`.`usersubmitid` = `users`.`id`
            WHERE `assignmentid` = ? AND (
                        LOWER(`users`.`username`) LIKE ? OR 
                        LOWER(`users`.`fullname`) LIKE ? OR 
                        LOWER(`ogfilename`) LIKE ? OR 
                        LOWER(`files`) LIKE ? OR 
                        LOWER(`lastupdate`) LIKE ?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $cur_assignment_id, $lowsearch, $lowsearch, $lowsearch, $lowsearch, $lowsearch);
            $stmt->execute();
            $result_1 = $stmt->get_result();
            $total_rows = $result_1->fetch_assoc()['total'];
            $total_pages = ceil($total_rows / $limit);
            ?>
            <section class="container">
                <div class="row">
                    <div class="col-md-2">
                        <!-- Title -->
                        <div class="small text-secondary"><sub>Title</sub></div>
                        <b><?php echo $cur_assignment_obj['title']; ?></b>
                        <!-- Teacher -->
                        <div class="small text-secondary"><sub>Teacher</sub></div>
                        <b><?php echo $cur_assignment_obj['teacherfullname']; ?></b>
                        <!-- Description -->
                        <div class="small text-secondary"><sub>Description</sub></div>
                        <b><?php echo $cur_assignment_obj['description']; ?></b>
                        <!-- Assignment -->
                        <div class="small text-secondary"><sub>Assignment File</sub></div>

                        <form method="POST">
                            <div>
                                <input class="btn btn-link" type="submit" name="download" value="Download">
                            </div>
                        </form>
                        <?php if (isset($user_error) && !empty($user_error)) { ?>
                            <!-- Error message -->
                            <div class="text-center"><a style="color:red"><?php echo $user_error; ?></a></div>
                        <?php } ?>
                    </div>
                    <div class="col-md-8">
                        <form class="d-flex" role="search">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                                <input type="hidden" name="id" value="<?php echo $cur_assignment_id; ?>">
                                <input class="form-control" type="search" name="search" placeholder="Search..." aria-label="Search" <?php echo isset($search) ? 'value="' . str_replace("%", "", $search) . '"' : '' ?>>
                            </div>
                        </form>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Username</th>
                                    <th>Filename</th>
                                    <th>File path</th>
                                    <th>Submit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($tbl_exercise->num_rows > 0) {
                                    while ($row = $tbl_exercise->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . $row['username'] . '</td>';
                                        echo '<td>' . $row['userfullname'] . '</td>';
                                        echo '<td>' . $row['ogfilename'] . '</td>';
                                        echo '<td>' . $row['files'] . '</td>';
                                        echo '<td>' . $row['lastupdate'] . '</td>';
                                        echo '<td style="text-align:center; width:1px; white-space:nowrap;">';
                                        echo '<a href="/download.php?tbl=submits&id=' . $row['id'] . '" class="btn btn-sm btn-info me-2"><i class="bi bi-download"></i></a>';
                                        echo '</td>';
                                        echo '</tr>';

                                        if (isset($_POST['download']) && ctype_digit($_POST['download']) && $cur_id_submit == $_POST['download']) {
                                            echo ("Downdload");
                                            downloadFile($cur_file_path, $row['ogfilename']);
                                        }
                                    }
                                } else {
                                    echo '<tr><td colspan="6">No records found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        // Previous and Next buttons for pagination
                        $search_param = "";
                        if (isset($search)) {
                            $search = str_replace("%", "", $search);
                        }
                        if ($search !== '') {
                            $search_param = '&search=' . $search;
                        }
                        if ($page > 1) {
                            echo '<a href="?id=' . $cur_assignment_id . '&page=' . ($page - 1) . $search_param . '" class="btn btn-primary">Previous</a>';
                        }
                        if ($page < $total_pages) {
                            echo '<a href="?id=' . $cur_assignment_id . '&page=' . ($page + 1) . $search_param . '" class="btn btn-primary">Next</a>';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
    <?php } ?>
    <!-- switch theme -->
    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
        <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (light)">
            <svg class="bi my-1 theme-icon-active" width="1em" height="1em">
                <use href="#"></use>
            </svg>
            <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
            Themes
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light" aria-pressed="true">
                    Light
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                    Dark
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                    Auto
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#"></use>
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</body>

</html>