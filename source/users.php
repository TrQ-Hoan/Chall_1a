<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['username'])){
    header('location:login.php');
}

$conn = mysqli_connect($MYSQL_HOST,$MYSQL_USERNAME,$MYSQL_PASSWORD);
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
mysqli_select_db($conn,$MYSQL_DB);

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : "%%";

$sql = "SELECT `username`,`fullname`,`email`,`phone`,`role` 
        FROM `users` WHERE
            `username` LIKE ? OR
            `fullname` LIKE ? OR 
            `email` LIKE ? OR 
            `phone` LIKE ? OR 
            `role` LIKE ?
        LIMIT $limit OFFSET $offset;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $search, $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total pages for pagination
$sql = "SELECT COUNT(*) AS total
        FROM `users` WHERE
            `username` LIKE ? OR
            `fullname` LIKE ? OR 
            `email` LIKE ? OR 
            `phone` LIKE ? OR 
            `role` LIKE ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $search, $search, $search, $search, $search);
$stmt->execute();
$result_1 = $stmt->get_result();
$total_rows = $result_1->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Hello World</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" style="margin-left: 3%;" href="#">QLSV</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Assigments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Challegens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav" style="margin-right: 3%;">
                    <li class="nav-item">
                        <a class="nav-link" href="/logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/account.php">Info</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Users</h2>
        <form class="d-flex form-outline mb-4" role="search">
            <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search"
            <?php
            if(isset($search)){
                $search = str_replace("%","",$search);
                echo 'value="' . $search .'"';
            }
            ?>
            >
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['username'] . '</td>';
                            echo '<td>' . $row['fullname'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['phone'] . '</td>';
                            echo '<td>' . $row['role'] . '</td>';
                            echo '<td>
                                        <a href="#" class="btn btn-primary">Edit</a>
                                        <a href="#" class="btn btn-danger">Remove</a>
                                        <a href="#" class="btn btn-info">Info</a>
                                    </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">No records found.</td></tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
            // Previous and Next buttons for pagination
            $search_param="";
            if (isset($search)){
                $search = str_replace("%","",$search);
            }
            if ($search !== ''){
                $search_param='&search='.$search;
            }
            if ($page > 1) {
                echo '<a href="?page='.($page - 1).$search_param.'" class="btn btn-primary">Previous</a>';
            }
            if ($page < $total_pages) {
                echo '<a href="?page='.($page + 1).$search_param.'" class="btn btn-primary">Next</a>';
            }
        ?>
    </div>
</body>

</html>