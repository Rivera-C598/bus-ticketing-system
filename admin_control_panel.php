<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Control Panel - Bus Ticketing System</title>
</head>
<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Welcome to The Admin Control Panel</h1>
            </div>
        </header>
        <main class="container my-5">
            <section id="admin-panel" class="mb-4 px-3">
                <div class="container text-center">
                    <h2>Admin Control Panel</h2>
                    <div class="row">
                        <div class="col-md-6">
                        <a href="manage_buses.php" class="btn btn-primary btn-lg btn-block">
                        Manage Buses
                        </a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-lg btn-block">
                                Records
                            </button>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-8 offset-md-2">
                            <input type="text" class="form-control" placeholder="Search by 6-Digit Code">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>6-Digit Code</th>
                                        <th>Student ID</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Insert data from your database here -->
                                    <tr>
                                        <td>123456</td>
                                        <td>ST12345</td>
                                        <td>2023-10-18</td>
                                        <td>
                                            <a href="#" class="btn btn-success">View</a>
                                            <a href="#" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="admin_logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; 2023 Bus Ticketing Service</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
