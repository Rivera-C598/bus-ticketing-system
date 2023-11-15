<?php
if (!isset($_GET['error'])) {
    $errorMessage = 'An unspecified error occurred.';
} else {
    $error = isset($_GET['error']) ? $_GET['error'] : 'unknown_error';
    if ($error == 'invalid_credentials') {
        $errorMessage = 'Invalid credentials.';
    } else if ($error == 'invalid_fare') {
        $errorMessage = 'Invalid fare. Please refresh the ticket form and book again.';
    } else if ($error == 'invalid_studentId') {
        $errorMessage = 'Student ID not found. If you are not a student, you can proceed directly to the bus ticketing cashier to book a bus.';
    } else {
        $errorMessage = 'Unknown error occurred.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        footer {
            flex-shrink: 0;
        }

        .container {
            max-width: 700px;
            word-wrap: break-word;

        }

        section {
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .error-message {
            color: red;
            display: inline;

        }
    </style>

    <title>Error</title>
</head>

<body>
    <div class="wrapper">

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        </nav>
        <main class="container my-5">
            <section id="home" class="mb-4 p-5 text-center">
                <div class="container">
                    <h2>Error: <span class="error-message"><?php echo $errorMessage; ?></span></h2>
                </div>
            </section>
        </main>
        <footer class="bg-dark text-white text-center py-3">
            <div class="container">
                <p>&copy; 2023 Bus Ticketing Service</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>