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

function generate_hash($password, $salt)
{
    return hash('sha256', $password . $salt);
}

function create_user($username, $password, $full_name, $email, $phone)
{
    global $conn;

    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `phone`, `role`) VALUES (?, ?, ?, ?, ?, 'student')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $full_name, $email, $phone);
    return $stmt->execute();
}

function update_user($username, $email, $phone, $password)
{
    global $conn;

    $sql = "UPDATE `users` SET `email` = ?, `phone` = ?" . (empty($password) ? "" : " `password = ?`") . " WHERE `username` = ?";
    $stmt = $conn->prepare($sql);
    if (empty($password)) {
        $stmt->bind_param("sss", $email, $phone, $username);
    } else {
        $stmt->bind_param("ssss", $email, $phone, $password, $username);
    }
    return $stmt->execute();
}

if ($_SESSION['user_role'] !== 'teacher' && $_SESSION['user_id'] !== $cur_user_id) {
    http_response_code(403);
    include("_err403.php");
    exit();
}

$page_stat_cfg = 'create_user';

if (isset($_POST['create_user'])) {
    $username = $_POST['accountUsername'];
    $password = $_POST['accountPassword'];
    $password2 = $_POST['accountRepeatPassword'];
    $full_name = $_POST['accountName'];
    $email = $_POST['accountEmail'];
    $phone = $_POST['accountPhone'];

    if (
        empty($username) || empty($password) || empty($password2) ||
        empty($full_name) || empty($email) || empty($phone)
    ) {
        $account_error = 'Please fill all form!';
    }

    if ($password !== $password2 && !isset($account_error)) {
        $account_error = 'Password not match!';
    }

    if (!create_user($username, $password, $full_name, $email, $phone)) {
        $account_error - 'Create new user error!';
    } else {
        header('location:users.php');
    }
}

if (isset($_POST['update_user'])) {
    $email = $_POST['accountEmail'];
    $phone = $_POST['accountPhone'];
    $password = $_POST['accountPassword'];
    $password2 = $_POST['accountRepeatPassword'];

    if (empty($email) && empty($phone)) {
        $account_error = 'Please fill all form!';
    }

    if ($password !== $password2 && !isset($account_error)) {
        $account_error = 'Password not match';
    }

    if (!update_user($_POST['update_user'], $email, $phone, $password)) {
        $account_error - 'Create new user error!';
    } else {
        header('location:users.php');
    }
}

if ($cur_user_id !== 0) {
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
    $page_stat_cfg = 'update_user';
}

// refill last submit
if (isset($_POST['update_user']) || isset($_POST['create_user'])) {
    if (!isset($cur_user_obj)) {
        $cur_user_obj = [
            'username' => isset($_POST['accountUsername']) ? $_POST['accountUsername'] : '',
            'fullname' => isset($_POST['accountName']) ? $_POST['accountName'] : '',
            'email' => '',
            'phone' => ''
        ];
    }
    $cur_user_obj['email'] = $_POST['accountEmail'];
    $cur_user_obj['phone'] = $_POST['accountPhone'];
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
    <section class="w-100 p-4 d-flex justify-content-center pb-4">
        <div style="width: 26rem;">
            <h2 class="form-outline mb-4">Account information</h2>
            <form method="POST" name="<?php echo $page_stat_cfg; ?>">
                <!-- Name input -->
                <div class="form-floating mb-4">
                    <input type="text" name="accountName" class="form-control" placeholder="Full Name" <?php echo isset($cur_user_obj) ? 'value="' . $cur_user_obj['fullname'] . '"' : '';
                                                                                                        echo $page_stat_cfg === 'update_user' ? ' readonly' : ''; ?> />
                    <label for="floatingInput">Full Name</label>
                </div>

                <!-- Username input -->
                <div class="form-floating mb-4">
                    <input type="text" name="accountUsername" class="form-control" placeholder="User Name" <?php echo isset($cur_user_obj) ? 'value="' . $cur_user_obj['username'] . '"' : '';
                                                                                                            echo $page_stat_cfg === 'update_user' ? ' readonly' : ''; ?> />
                    <label for="floatingInput">User Name</label>
                </div>

                <!-- Email input -->
                <div class="form-floating mb-4">
                    <input type="email" name="accountEmail" class="form-control" placeholder="Email" <?php echo isset($cur_user_obj) ? 'value="' . $cur_user_obj['email'] . '"' : ''; ?> />
                    <label for="floatingInput">Email</label>
                </div>

                <!-- Phone number -->
                <div class="form-floating mb-4">
                    <input type="text" name="accountPhone" class="form-control" placeholder="Phone" <?php echo isset($cur_user_obj) ? 'value="' . $cur_user_obj['phone'] . '"' : ''; ?> />
                    <label for="floatingInput">Phone</label>
                </div>

                <!-- Password input -->
                <div class="form-floating mb-4">
                    <input type="password" name="accountPassword" class="form-control" placeholder="Password" />
                    <label for="floatingInput">Password</label>
                </div>

                <!-- Repeat Password input -->
                <div class="form-floating mb-4">
                    <input type="password" name="accountRepeatPassword" class="form-control" placeholder="Repeat password" />
                    <label for="floatingInput">Repeat password</label>
                </div>
                <?php if (isset($account_error) && !empty($account_error)) { ?>
                    <div class="text-center">
                        <a style="color:red"><?php echo $account_error; ?></a>
                    </div>
                <?php } ?>
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-3" name="<?php echo $page_stat_cfg; ?>" <?php echo isset($cur_user_obj) ? 'value="' . $cur_user_obj['username'] . '"' : ''; ?>>
                    <?php echo isset($cur_user_obj) ? 'Save' : 'Create'; ?>
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