<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Login - Bus Ticketing System</title>
</head>

<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Welcome to The Admin Panel</h1>
            </div>
        </header>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h1 class="text-center">Admin Login</h1>
                    <form action="admin_authenticate.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" id="password" required />
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Log in</button>
                            <a href="admin_registration.php" class="btn btn-success">Admin Registration</a>
                            <a href="index.html" class="btn btn-secondary">Go Back to Website</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--<main class="container my-5">
            <section id="admin-login" class="mb-4 px-3 text-center">
                <div class="container">
                    <h2>Admin Login</h2>
                    <form action="admin_authenticate.php" method="post">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        
                    </form>
                </div>
            </section>
        </main> -->
    </div>
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; 2023 Bus Ticketing Service</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>