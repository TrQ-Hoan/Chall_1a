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

$cur_user_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM `users` WHERE `id` = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cur_user_id);
$stmt->execute();
$result = $stmt->get_result();
$cur_user_obj = $result->fetch_assoc();
if (!isset($cur_user_obj)) {
    http_response_code(404);
    include("_err404.php");
    exit();
}
$stmt->close();

$send_btn = 'send';
$msg_edit = '';

if (isset($_GET['del']) && ctype_digit($_GET['del'])) {
    $del_msg_id = (int)$_GET['del'];
    $sql = "UPDATE `messages` SET `content` = '' WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $del_msg_id);
    if (!$stmt->execute()) {
        $user_error = 'Delete message fail';
    }
    $stmt->close();
    header('location:user.php?id=' . $_GET['id']);
}

if (isset($_GET['edit'])) {
    $send_btn = 'edit';
}

if (isset($_POST['edit']) && !empty($_POST['content'])) {
    $edit_msg_id = (int)$_GET['edit'];
    $sql = "UPDATE `messages` SET `content` = ? WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_POST['content'], $edit_msg_id);
    if (!$stmt->execute()) {
        $user_error = 'Edit message fail';
    }
    $stmt->close();
    header('location:user.php?id=' . $_GET['id']);
}

if (isset($_POST['send']) && !empty($_POST['content'])) {
    $sql = "INSERT INTO `messages` (`idsend`,`idrec`,`content`) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $_SESSION['user_id'], $cur_user_id, $_POST['content']);
    if (!$stmt->execute()) {
        $user_error = 'Send message fail';
    }
    $stmt->close();
}

$sql = "SELECT * FROM `messages` WHERE (`idsend` = ? AND `idrec` = ?) OR (`idsend` = ? AND `idrec` = ?);";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $_SESSION['user_id'], $cur_user_id, $cur_user_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang='en' data-bs-theme="auto">

<head>
    <meta charset="UTF-8">
    <title>QLSV - Account</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIxMDUuODEgLTE4LjExMSA0OCA0OCIgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzPgogICAgPGZpbHRlciBpZD0iZWRpdGluZy1nbG93aW5nIiB4PSItMTAwJSIgeT0iLTEwMCUiIHdpZHRoPSIzMDAlIiBoZWlnaHQ9IjMwMCUiPgogICAgICA8ZmVHYXVzc2lhbkJsdXIgaW49IlNvdXJjZUdyYXBoaWMiIHJlc3VsdD0iYmx1ciIgc3RkRGV2aWF0aW9uPSI2Ii8+CiAgICAgIDxmZU1lcmdlPgogICAgICAgIDxmZU1lcmdlTm9kZSBpbj0iYmx1ciIvPgogICAgICAgIDxmZU1lcmdlTm9kZSBpbj0iU291cmNlR3JhcGhpYyIvPgogICAgICA8L2ZlTWVyZ2U+CiAgICA8L2ZpbHRlcj4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZWRpdGluZy1nbG93aW5nLWdyYWRpZW50IiB4MT0iMCIgeDI9IjEiIHkxPSIwLjUiIHkyPSIwLjUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiM2N2ZmNDMiLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjOTBmZmZmIi8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICA8cmVjdCB4PSI5My42ODMiIHk9Ii0yNC41NjUiIHdpZHRoPSI2OS45NzQiIGhlaWdodD0iNjUuMzcyIiBzdHlsZT0iZmlsbDogcmdiKDM3LCAzNywgMzcpOyIvPgogIDxnIGZpbHRlcj0idXJsKCNlZGl0aW5nLWdsb3dpbmcpIiB0cmFuc2Zvcm09Im1hdHJpeCgxLCAwLCAwLCAxLCAtMTIwLjk5MzU0NTUzMjIyNjU2LCAtNzAuMzY4NDYxNjA4ODg2NzIpIj4KICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIzNi43NzUwMDc3MjQ3NjE5NiwgODcuMTE5OTk5ODg1NTU5MDgpIj4KICAgICAgPHBhdGggZD0iTTIuNjUtMTEuNzZMMi42NS0xMS43NlEyLjY1LTE1LjMzIDUuNTQtMTYuNjZMNS41NC0xNi42Nkw1LjU0LTE2LjY2UTYuOTQtMTcuMzEgOC44Ny0xNy4zMUw4Ljg3LTE3LjMxTDguODctMTcuMzFRMTAuMTMtMTcuMzEgMTEuMzEtMTdMMTEuMzEtMTdMMTEuMzEtMTdRMTIuNDgtMTYuNjkgMTMuMjktMTYuMTJMMTMuMjktMTYuMTJMMTMuMjktMjMuODBMMTUuMjAtMjMuODBMMTUuMjAgMEwxMy4zMyAwLjE3TDEzLjMzIDAuMTdRMTEuMTIgMC40MSA5Ljg2IDAuNDFMOS44NiAwLjQxTDkuODYgMC40MVE4LjYwIDAuNDEgNy41MCAwLjE3TDcuNTAgMC4xN0w3LjUwIDAuMTdRNi4zOS0wLjA3IDUuMzAtMC42MUw1LjMwLTAuNjFMNS4zMC0wLjYxUTQuMDgtMS4yMiAzLjM3LTIuNDFMMy4zNy0yLjQxTDMuMzctMi40MVEyLjY1LTMuNjAgMi42NS01LjM0TDIuNjUtNS4zNEwyLjY1LTExLjc2Wk0xMy4yOS0xNC40NUwxMy4yOS0xNC40NVExMi4zOC0xNC45NiAxMS4xOS0xNS4yOEwxMS4xOS0xNS4yOEwxMS4xOS0xNS4yOFExMC4wMC0xNS42MSA4Ljg0LTE1LjYxTDguODQtMTUuNjFMOC44NC0xNS42MVE2Ljk0LTE1LjYxIDUuNzMtMTQuNjdMNS43My0xNC42N0w1LjczLTE0LjY3UTQuNTItMTMuNzQgNC41Mi0xMS42Nkw0LjUyLTExLjY2TDQuNTItNS4zMEw0LjUyLTUuMzBRNC41Mi0xLjMzIDEwLjAwLTEuMzNMMTAuMDAtMS4zM0wxMC4wMC0xLjMzUTExLjI5LTEuMzMgMTMuMjktMS42M0wxMy4yOS0xLjYzTDEzLjI5LTE0LjQ1Wk0yMS40MiAwLjQ0TDIxLjQyIDAuNDRRMTkuNTItMC4xMCAxOC42Ny0xLjI5TDE4LjY3LTEuMjlMMTguNjctMS4yOVExNy44Mi0yLjQ4IDE3LjgyLTQuNDlMMTcuODItNC40OUwxNy44Mi0yMy44MEwxOS43Mi0yMy44MEwxOS43Mi0xNy4wM0wyMy44MC0xNy4wM0wyMi45OC0xNS4yM0wxOS43Mi0xNS4yM0wxOS43Mi00LjQ5TDE5LjcyLTQuNDlRMTkuNzItMS43NyAyMS45Ni0xLjAyTDIxLjk2LTEuMDJMMjEuNDIgMC40NFoiIGZpbGw9InVybCgjZWRpdGluZy1nbG93aW5nLWdyYWRpZW50KSIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+#CqQkestbA4">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/color-modes.js"></script>
    <style>
        .chat-time {
            font-size: 0.8rem;
            color: white;
            opacity: 0.6;
            margin-top: 4px;
            text-align: right;
        }
    </style>
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
    <div class="container mt-4">
        <h2 class="mb-4">User info</h2>
        <section class="container">
            <div class="row">
                <div class="col-md-2">
                    <form>
                        <!-- Name -->
                        <div class="small text-secondary"><sub>Full Name</sub></div>
                        <b><?php echo $cur_user_obj['fullname']; ?> </b>
                        <!-- Username -->
                        <div class="small text-secondary"><sub>User Name</sub></div>
                        <b><?php echo $cur_user_obj['username']; ?> </b>
                        <!-- Email -->
                        <div class="small text-secondary"><sub>Email</sub></div>
                        <b><?php echo $cur_user_obj['email']; ?> </b>
                        <!-- Phone number -->
                        <div class="small text-secondary"><sub>Phone</sub><b></div>
                        <?php echo $cur_user_obj['phone']; ?> </b>
                        <!-- Error message -->
                        <div class="text-center"><a style="color:red"><?php echo $user_error; ?></a></div>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="border rounded p-3">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <div class="d-flex mb-2 mb-sm-0">
                                <div class="d-block flex-grow-1">
                                    <h6 class="mb-0 mt-1"><?php echo $cur_user_obj['fullname']; ?></h6>
                                    <div class="small text-secondary">email: <b><?php echo $cur_user_obj['email']; ?></b></div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Chat messages will be displayed here -->
                        <div style="height: 400px; overflow-y: scroll;" id="scrollContainer">
                            <?php
                            while ($message = $result->fetch_assoc()) {
                                if (isset($_GET['edit']) && (int)$_GET['edit'] === $message['id']) {
                                    $msg_edit = $message['content'];
                                }
                                if ($message['idsend'] == $_SESSION['user_id']) {
                                    echo '<div class="d-flex justify-content-end text-end mb-1"><div class="w-100"><div class="d-flex flex-column align-items-end">';
                                    if (empty($message['content'])) {
                                        echo '<div class="bg-primary text-white-50 p-2 px-3 rounded-2" style="max-width: 70%;"><i>message has deleted</i>';
                                        echo '<div class="chat-time">';
                                    } else {
                                        echo '<div class="bg-primary text-white p-2 px-3 rounded-2" style="max-width: 70%;">' . htmlspecialchars($message['content']);
                                        echo '<div class="chat-time">';
                                        echo '<a class="chat-time" href="/user.php?id=' . $cur_user_id . '&del=' . $message['id'] . '">(Del)</a> | ';
                                        echo '<a class="chat-time" href="/user.php?id=' . $cur_user_id . '&edit=' . $message['id'] . '">(Edit)</a> | ';
                                    }
                                    echo $message['createat'] . ($message['lastupdate'] > $message['createat'] ? ' (edited) ' : ' ') . '</div>';
                                    echo '</div></div></div></div>';
                                } else {
                                    echo '<div class="flex-grow-1 mb-1"><div class="w-100"><div class="d-flex flex-column align-items-start">';
                                    if (empty($message['content'])) {
                                        echo '<div class="bg-secondary text-black-50 border text-secondary p-2 px-3 rounded-2" style="max-width: 70%;"><i>message has deleted</i>';
                                    } else {
                                        echo '<div class="bg-secondary text-white border text-secondary p-2 px-3 rounded-2" style="max-width: 70%;">' . htmlspecialchars($message['content']);
                                    }
                                    echo '<div class="chat-time">' . $message['createat'] . ($message['lastupdate'] > $message['createat'] ? ' (edited) ' : ' ') . '</div>';
                                    echo '</div></div></div></div>';
                                }
                            ?>
                            <?php
                            }

                            $stmt->close(); ?>
                            <!-- ... Add more chat boxes here ... -->
                        </div>
                    </div>
                    <form method="POST" name="<?php echo $send_btn; ?>">
                        <div class="input-group mt-3">
                            <input type="text" class="form-control" name="content" placeholder="Type your message" <?php echo $send_btn !== 'send' ? 'value="' . $msg_edit . '"' : ''; ?>>
                            <button class="btn btn-primary" name="<?php echo $send_btn; ?>"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
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
<script>
    window.onload = function() {
        var scrollContainer = document.getElementById("scrollContainer");
        scrollContainer.scrollTop = scrollContainer.scrollHeight;
    };
</script>

</html>