<?php
require_once 'ConnectDB.php';
session_start();
$conn = mysqli_connect($MYSQL_HOST,$MYSQL_USERNAME,$MYSQL_PASSWORD);
if (!$conn) {
    die(mysqli_connect_errno() . ':' . mysqli_connect_error());
}
mysqli_select_db($conn,$MYSQL_DB);

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1;
}

function is_logged_in() {
    return isset($_SESSION['user_name']);
}

function generate_hash($password, $salt) {
    return hash('sha256', $password . $salt);
}

function create_user($username, $password, $full_name, $email, $phone) {
    global $conn;
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, full_name, email, phone, is_admin) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $hashed_password, $full_name, $email, $phone, $is_admin);
    
    $is_admin = is_admin() ? 1 : 0;
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function update_user_info($username, $full_name, $email, $phone) {
    global $conn;
    
    $sql = "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $full_name, $email, $phone, $username);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}


if (isset($_POST['login'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $stored_hash = $user['password'];
        $salt = $user['salt'];

        $input_hash = generate_hash($input_password, $salt);

        if ($input_hash === $stored_hash) {
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // Direct when login success
            header("Location: index.php");
            exit();
        } else {
            // Wrong password
            $login_error = "Invalid username or password";
        }
    } else {
        // Invalid user
        $login_error = "Invalid username or password";
    }
}


if (isset($_POST['create_user'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $full_name = $_POST['new_full_name'];
    $email = $_POST['new_email'];
    $phone = $_POST['new_phone'];
    
    if (create_user($username, $password, $full_name, $email, $phone)) {
        // Thành công, thực hiện hành động sau khi tạo user
    } else {
        // Lỗi, xử lý thông báo lỗi
    }
}

if (isset($_POST['update_user_info'])) {
    $full_name = $_POST['user_full_name'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone'];
    
    if (update_user_info($_SESSION['user_name'], $full_name, $email, $phone)) {
        // Thành công, thực hiện hành động sau khi cập nhật thông tin
    } else {
        // Lỗi, xử lý thông báo lỗi
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
        <form>
        <!-- Name input -->
        <div class="form-outline mb-4">
            <input type="text" id="registerName" class="form-control" placeholder="Full Name"/>
        </div>

        <!-- Username input -->
        <div class="form-outline mb-4">
            <input type="text" id="registerUsername" class="form-control" placeholder="Username"/>
        </div>

        <!-- Email input -->
        <div class="form-outline mb-4">
            <input type="email" id="registerEmail" class="form-control" placeholder="Email"/>
        </div>

        <!-- Password input -->
        <div class="form-outline mb-4">
            <input type="password" id="registerPassword" class="form-control" placeholder="Password"/>
        </div>

        <!-- Repeat Password input -->
        <div class="form-outline mb-4">
            <input type="password" id="registerRepeatPassword" class="form-control" placeholder="Repeat password"/>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block mb-3">Create account for student</button>
        </form>
    </div>
</section>
</body>
</html>