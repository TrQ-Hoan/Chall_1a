<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}

if ($_SESSION['user_role'] !== 'teacher') {
    include("_err403.php");
    exit();
}

function uploadFile(array $mfile, $fileDest)
{
    $fileName = $mfile['name'];
    $fileTmpName = $mfile['tmp_name'];
    $fileSize = $mfile['size'];
    $fileError = $mfile['error'];
    $fileType = $mfile['type'];
    $fileDestination = $_SERVER['DOCUMENT_ROOT'] . $fileDest;
    $fileActualExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('txt');
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

$cur_challenge_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
$page_stat_cfg = 'create_challenge';

if ($cur_challenge_id !== 0) {
    $sql = "SELECT `challenges`.*,`users`.`username` AS `teacherusername`,`users`.`fullname` AS `teacherfullname`
    FROM `challenges` JOIN `users` ON `challenges`.`teacherid` = `users`.`id` WHERE `challenges`.`id` = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cur_challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cur_chall_obj = $result->fetch_assoc();
    if (!isset($cur_chall_obj)) {
        http_response_code(404);
        include("_err404.php");
        exit();
    }
    $page_stat_cfg = 'update_challenge';
}

if (isset($_POST['create_challenge'])) {
    $mfile = $_FILES['challFile'];
    $fileDestination = '/archive/challenges/' . $mfile['name'];
    $ret_upload = uploadFile($mfile, $fileDestination);
    if (!$ret_upload || empty($_POST['challTitle']) || empty($_POST['challHint'])) {
        // http_response_code(500);
        die("Request wrong or upload faild");
    }
    $sql = "INSERT INTO `challenges` (`teacherid`, `title`, `files`, `hints`)
    VALUES (?, ?, ?, ?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $_SESSION['user_id'], $_POST['challTitle'], $fileDestination, $_POST['challHint']);
    $stmt->execute();
    header('location:challenges.php');
}

if (isset($_POST['update_challenge'])) {
    $chall_id = (int)$_POST['update_challenge'];
    $mfile = $_FILES['challFile'];
    if (isset($mfile['name'])) {
        $fileDestination = '/archive/challenges/' . $mfile['name'];
        $ret_upload = uploadFile($mfile, $fileDestination);
        if (!$ret_upload || empty($_POST['challTitle']) || empty($_POST['challHint'])) {
            // http_response_code(500);
            die("Request wrong or upload faild");
        }
        $sql = "UPDATE `challenges` SET `teacherid` = ?, `title` = ?, `files` = ?, `hints` = ? WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $_SESSION['user_id'], $_POST['challTitle'], $fileDestination, $_POST['challHint'], $chall_id);
    } else {
        $sql = "UPDATE `challenges` SET `teacherid` = ?, `title` = ?, `hints` = ? WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $_SESSION['user_id'], $_POST['challTitle'], $_POST['challHint'], $chall_id);
    }
    $stmt->execute();
    header('location:challenges.php');
}

?>

<!DOCTYPE html>
<html lang='en' data-bs-theme="auto">

<head>
    <title>QLSV - Challenge</title>
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
    <section class="w-100 p-4 d-flex justify-content-center pb-4">
        <div style="width: 26rem;">
            <h2 class="form-outline mb-4">Challenge information</h2>
            <form method="POST" enctype="multipart/form-data" name="<?php echo $page_stat_cfg; ?>">
                <!-- Title input -->
                <div class="form-floating mb-4">
                    <input type="text" name="challTitle" class="form-control" placeholder="Title" <?php echo isset($cur_chall_obj) ? 'value="' . $cur_chall_obj['title'] . '"' : ''; ?> />
                    <label for="floatingInput">Title</label>
                </div>

                <?php if ($page_stat_cfg === 'update_challenge') { ?>
                    <!-- Teacher username -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" placeholder="Teacher Username" <?php echo isset($cur_chall_obj) ? 'value="' . $cur_chall_obj['teacherfullname'] . '"' : ''; ?> readonly />
                        <label for="floatingInput">Teacher Username</label>
                    </div>
                <?php } ?>

                <!-- Hints input -->
                <div class="form-floating mb-4 h-100">
                    <textarea type="text" name="challHint" class="form-control" placeholder="Hints" style="min-height:100px;"><?php echo isset($cur_chall_obj) ? $cur_chall_obj['hints'] : ''; ?></textarea>
                    <label for="floatingInput">Hints</label>
                </div>

                <?php if ($page_stat_cfg === 'update_challenge') { ?>
                    <!-- Files upload -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" placeholder="File" <?php echo isset($cur_chall_obj) ? 'value="' . $cur_chall_obj['files'] . '"' : ''; ?> readonly />
                        <label for="floatingInput">File</label>
                    </div>
                <?php } ?>

                <!-- Button upload file -->
                <div class="mb-4">
                    <input class="form-control" type="file" name="challFile">
                </div>
                <?php if (isset($challenge_error) && !empty($challenge_error)) { ?>
                    <div class="text-center">
                        <a style="color:red"><?php echo $challenge_error; ?></a>
                    </div>
                <?php } ?>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-3" name="<?php echo $page_stat_cfg; ?>" <?php echo isset($cur_chall_obj) ? 'value="' . $cur_chall_obj['id'] . '"' : ''; ?>>
                    <?php echo isset($cur_chall_obj) ? 'Save' : 'Create'; ?>
                </button>
            </form>
        </div>
    </section>
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