
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Admin Registration - Bus Ticketing System</title>
</head>
<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Welcome to Admin Registration</h1>
            </div>
        </header>
        <main class="container my-5">
            <section id="admin-registration" class="mb-4 px-3 text-center">
                <div class="container">
                    <h2>Admin Registration</h2>
                    <form action="admin_register.php" method="post">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="verification_code">Verification Code:</label>
                            <input type="text" class="form-control" id="verification_code" name="verification_code" required>
                        </div>
                        <a href="admin_login.php" class="btn btn-primary">Go back to Login</a>
                        <button type="submit" class="btn btn-success">Register</button>
                    </form>
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
