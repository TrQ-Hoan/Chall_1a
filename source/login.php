<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['user_name'])) {
    header('location:index.php');
}

$conn = mysqli_connect($MYSQL_HOST, $MYSQL_USERNAME, $MYSQL_PASSWORD);
if (!$conn) {
    die(mysqli_connect_errno() . ':' . mysqli_connect_error());
}
mysqli_select_db($conn, $MYSQL_DB);

if (isset($_POST['login'])) {
    $input_username = $_POST['loginName'];
    $input_password = $_POST['loginPassword'];

    $sql = "SELECT * FROM `users` WHERE `username` = ? AND `password` = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $input_username, $input_password);
    $stmt->execute();
    $result = $stmt->get_result();
    $conn->close();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];

        // Direct when login success
        header('location:index.php');
    } else {
        // Invalid user or Wrong password
        $login_error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <section class="w-100 p-4 d-flex justify-content-center pb-4">
        <div style="width: 26rem;">
            <h1 class="form-outline mb-4">Login</h1>
            <form method="POST" name="login">
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input type="text" name="loginName" class="form-control" placeholder="User Name" />
                </div>

                <!-- Password input -->
                <div class="form-outline mb-4">
                    <input type="password" name="loginPassword" class="form-control" placeholder="Password" />
                </div>

                <div class="text-center">
                    <a style="color:red"><?php echo $login_error; ?></a>
                </div>
                <div class="text-center form-outline mb-4">
                    <!-- Simple link -->
                    <a href="#!">Forgot password?</a>
                </div>

                <!-- Submit button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-outline-primary btn-block mb-4" name="login">Sign in</button>
                </div>
                <?php
                if (isset($login_error)) {
                    unset($login_error);
                }
                ?>
            </form>
        </div>
    </section>
</body>

</html>