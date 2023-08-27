<?php
require_once 'ConnectDB.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang='en' data-bs-theme="auto">

<head>
    <title>QLSV</title>
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