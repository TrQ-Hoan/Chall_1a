<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['username'])){
    header('location:login.php');
}
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
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Assigments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Challegens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users.php">Users</a>
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
                <!-- <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form> -->
            </div>
        </div>
    </nav>
</body>

</html>