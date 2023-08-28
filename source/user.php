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
        <form>
            <!-- Name -->
            <div>Full Name: <b><?php echo $cur_user_obj['fullname']; ?> </b></div>

            <!-- Username -->
            <div>User Name: <b><?php echo $cur_user_obj['username']; ?> </b></div>

            <!-- Email -->
            <div>Email: <b><?php echo $cur_user_obj['email']; ?> </b></div>

            <!-- Phone number -->
            <div>Phone: <b><?php echo $cur_user_obj['phone']; ?> </b></div>

            <!-- Error message -->
            <div class="text-center"><a style="color:red"><?php echo $account_error; ?></a></div>

        </form>
        <section class="w-100 d-flex justify-content-center pb-4">
            <div class="container">
                <div class="row gx-0">
                    <!-- Chat conversation START -->
                    <div class="col-lg-8 col-xxl-9">
                        <div class="card card-chat rounded-start-lg-0 border-start-lg-0">
                            <div class="card-body h-100">
                                <div class="tab-content py-0 mb-0 h-100" id="chatTabsContent">
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane show active h-100" id="chat-1" role="tabpanel" aria-labelledby="chat-1-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                </div>
                                                <div class="d-block flex-grow-1">
                                                    <h6 class="mb-0 mt-1">Judy Nguyen</h6>
                                                    <div class="small text-secondary"><i class="fa-solid fa-circle text-success me-1"></i>Online</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition os-host-scrollbar-vertical-hidden">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px; height: 1048px; width: 724px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: auto; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Applauded no discovery in newspaper allowance am northward😊</div>
                                                                        <div class="small my-2">6:15 AM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">With pleasure</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">No visited raising gravity outward subject my cottage Mr be.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">6:20 AM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check-double text-info"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Please find the attached updated files</div>
                                                                        <!-- Files START -->
                                                                        <!-- Files END -->
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">How promotion excellent curiosity yet attempted happiness Gay prosperous impression😮</div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">
                                                                            <p class="small mb-0">Congratulations:)</p>
                                                                            <div class="card shadow-none p-2 border border-2 rounded mt-2">
                                                                                <img src="assets/images/elements/14.svg" alt="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">And sir dare view but over man So at within mr to simple assure Mr disposing.</div>
                                                                    <!-- Images -->
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">5:35 PM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <img class="rounded h-200px" src="assets/images/avatar/05.jpg" alt="">
                                                                    <div class="small my-2">5:36 PM</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">2 New Messages</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-2">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Traveling alteration impression 🤐 six all uncommonly Chamber hearing inhabit joy highest private.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/10.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-3 rounded-2">
                                                                            <div class="typing d-flex align-items-center">
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="height: 100%; transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane h-100" id="chat-2" role="tabpanel" aria-labelledby="chat-2-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                </div>
                                                <div class="d-block flex-grow-1">
                                                    <h6 class="mb-0 mt-1">Carolyn Ortiz</h6>
                                                    <div class="small text-secondary"><i class="fa-solid fa-circle text-danger me-1"></i>Last active 2 days</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown2" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown2">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content overflow-auto custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Night signs creeping yielding green Seasons.</div>
                                                                        <div class="small my-2">6:15 AM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Creeping earth under was You're without which image.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">6:20 AM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check-double text-info"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Thank you for prompt response</div>
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Won't that fish him whose won't also. </div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Moving living second beast Over fish place beast.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">5:35 PM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">2 New Messages</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Thing they're fruit together forth day.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">
                                                                            Fly replenish third to said void life night yielding for heaven give blessed spirit.</div>
                                                                        <div class="small my-2">9:30 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane h-100" id="chat-3" role="tabpanel" aria-labelledby="chat-3-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <img class="avatar-img rounded-circle" src="assets/images/avatar/12.jpg" alt="">
                                                </div>
                                                <div class="d-block flex-grow-1">
                                                    <h6 class="mb-0 mt-1">Billy Vasquez</h6>
                                                    <div class="small text-secondary">Last active a month</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown3" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown3">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content overflow-auto custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Hello</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Made and For saw Creepeth place shall Moving.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">6:20 AM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check-double text-info"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/12.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Thank you for prompt response</div>
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/12.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-3 rounded-2">
                                                                            <div class="typing d-flex align-items-center">
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane h-100" id="chat-4" role="tabpanel" aria-labelledby="chat-4-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <ul class="avatar-group avatar-group-two">
                                                        <li class="avatar avatar-xs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/02.jpg" alt="avatar">
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="flex-grow-1 d-block">
                                                    <h6 class="mb-0 mt-1">Dennis, Ortiz</h6>
                                                    <div class="small text-secondary">Ortiz: I'm adding jhon</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown4" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown4">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content overflow-auto custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Firmament day life also let subdue.</div>
                                                                        <div class="small my-2">6:15 AM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Yes</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Hold do at tore in park feet near my case.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">6:20 AM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check-double text-info"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">78958642-589</div>
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Void Fowl greater upon moveth bring gathering.</div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message right -->
                                                        <div class="d-flex justify-content-end text-end mb-1">
                                                            <div class="w-100">
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <div class="bg-primary text-white p-2 px-3 rounded-2">Kind had stars cattle Good fill divide Multiply.</div>
                                                                    <div class="d-flex my-2">
                                                                        <div class="small text-secondary">5:35 PM</div>
                                                                        <div class="small ms-2"><i class="fa-solid fa-check"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">2 New Messages</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">She'd Darkness beast don't deep One above.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Signs creepeth replenish which fourth may Seasons.</div>
                                                                        <div class="small my-2">9:30 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane h-100" id="chat-5" role="tabpanel" aria-labelledby="chat-5-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <ul class="avatar-group avatar-group-three">
                                                        <li class="avatar avatar-xs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/03.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/04.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/05.jpg" alt="avatar">
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="flex-grow-1 d-block">
                                                    <h6 class="mb-0 mt-1">Knight, Billy, Bryan</h6>
                                                    <div class="small text-secondary">Billy: Thank you!</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown5" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown5">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content overflow-auto custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/01.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Night signs creeping yielding green Seasons.</div>
                                                                        <div class="small my-2">6:15 AM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/02.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Thank you for prompt response</div>
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/03.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Won't that fish him whose won't also. </div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">2 New Messages</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Thing they're fruit together forth day.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/11.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">
                                                                            Fly replenish third to said void life night yielding for heaven give blessed spirit.</div>
                                                                        <div class="small my-2">9:30 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                    <!-- Conversation item START -->
                                    <div class="fade tab-pane h-100" id="chat-6" role="tabpanel" aria-labelledby="chat-6-tab">
                                        <!-- Top avatar and status START -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <div class="d-flex mb-2 mb-sm-0">
                                                <div class="flex-shrink-0 avatar me-2">
                                                    <ul class="avatar-group avatar-group-four">
                                                        <li class="avatar avatar-xxs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/06.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xxs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/07.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xxs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/08.jpg" alt="avatar">
                                                        </li>
                                                        <li class="avatar avatar-xxs">
                                                            <img class="avatar-img rounded-circle" src="assets/images/avatar/placeholder.jpg" alt="avatar">
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="flex-grow-1 d-block overflow-hidden">
                                                    <h6 class="mb-0 mt-1 text-truncate w-75">Webestica crew </h6>
                                                    <div class="small text-secondary">You: Okay thanks, everyone.</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Call button -->
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Audio call" data-bs-original-title="Audio call"><i class="bi bi-telephone-fill"></i></a>
                                                <a href="#!" class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Video call" data-bs-original-title="Video call"><i class="bi bi-camera-video-fill"></i></a>
                                                <!-- Card action START -->
                                                <div class="dropdown">
                                                    <a class="icon-md rounded-circle btn btn-primary-soft me-2 px-2" href="#" id="chatcoversationDropdown6" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></a>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatcoversationDropdown6">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-lg me-2 fw-icon"></i>Mark as read</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-mic-mute me-2 fw-icon"></i>Mute conversation</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-check me-2 fw-icon"></i>View profile</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2 fw-icon"></i>Delete chat</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2 fw-icon"></i>Archive chat</a></li>
                                                    </ul>
                                                </div>
                                                <!-- Card action END -->
                                            </div>
                                        </div>
                                        <!-- Top avatar and status END -->
                                        <hr>
                                        <!-- Chat conversation START -->
                                        <div class="chat-conversation-content custom-scrollbar os-host os-theme-dark os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-scrollbar-vertical-hidden os-host-transition">
                                            <div class="os-resize-observer-host observed">
                                                <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
                                            </div>
                                            <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
                                                <div class="os-resize-observer"></div>
                                            </div>
                                            <div class="os-content-glue" style="margin: 0px;"></div>
                                            <div class="os-padding">
                                                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow: visible;">
                                                    <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">Jul 16, 2022, 06:15 am</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/02.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Applauded no discovery in newspaper allowance am northward😍</div>
                                                                        <div class="small my-2">6:15 AM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/03.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Please find the attached updated files</div>
                                                                        <!-- Files START -->
                                                                        <!-- Files END -->
                                                                        <div class="small my-2">12:16 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/04.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">How promotion excellent 🥰 curiosity yet attempted happiness Gay prosperous impression.</div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat time -->
                                                        <div class="text-center small my-2">2 New Messages</div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-2">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/05.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Traveling alteration impression six all uncommonly Chamber hearing inhabit joy highest privat.</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/06.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-2 px-3 rounded-2">Attempted happiness Gay prosperous impression.</div>
                                                                        <div class="small my-2">3:22 PM</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat message left -->
                                                        <div class="d-flex mb-1">
                                                            <div class="flex-shrink-0 avatar avatar-xs me-2">
                                                                <img class="avatar-img rounded-circle" src="assets/images/avatar/07.jpg" alt="">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="w-100">
                                                                    <div class="d-flex flex-column align-items-start">
                                                                        <div class="bg-light text-secondary p-3 rounded-2">
                                                                            <div class="typing d-flex align-items-center">
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                                <div class="dot"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-unusable os-scrollbar-auto-hidden">
                                                <div class="os-scrollbar-track os-scrollbar-track-off">
                                                    <div class="os-scrollbar-handle" style="transform: translate(0px, 0px);"></div>
                                                </div>
                                            </div>
                                            <div class="os-scrollbar-corner"></div>
                                        </div>
                                        <!-- Chat conversation END -->
                                    </div>
                                    <!-- Conversation item END -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-sm-flex align-items-end">
                                    <textarea class="form-control mb-sm-0 mb-3" data-autoresize="" placeholder="Type a message" rows="1" style="height: 41px;"></textarea>
                                    <button class="btn btn-sm btn-danger-soft ms-sm-2"><i class="fa-solid fa-face-smile fs-6"></i></button>
                                    <button class="btn btn-sm btn-secondary-soft ms-2"><i class="fa-solid fa-paperclip fs-6"></i></button>
                                    <button class="btn btn-sm btn-primary ms-2"><i class="fa-solid fa-paper-plane fs-6"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Chat conversation END -->
                </div> <!-- Row END -->
                <!-- Chat END -->
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

</html>