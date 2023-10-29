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
    <title>Manage Buses - Bus Ticketing System</title>
</head>
<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Manage Buses</h1>
            </div>
        </header>
        <main class="container my-5">
            <section id="manage-buses" class="mb-4 px-3">
                <div class="container text-center">
                    <h2>Add Bus</h2>
                    <form action="add_bus.php" method="post">
                        <div class="form-group">
                            <label for="bus_id">Bus ID:</label>
                            <input type="text" class="form-control" id="bus_id" name="bus_id" required>
                        </div>
                        <div class="form-group">
                            <label for="route">Route:</label>
                            <input type="text" class="form-control" id="route" name="route" required>
                        </div>
                        <div class="form-group">
                            <label for="fare">Fare:</label>
                            <input type="text" class="form-control" id="fare" name="fare" required>
                        </div>
                        <div class="form-group">
                            <label for="capacity">Capacity:</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="air_conditioned" name="air_conditioned">
                            <label class="form-check-label" for="air_conditioned">Air Conditioned</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <div class="container">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Bus ID</th>
                        <th>Route</th>
                        <th>Fare</th>
                        <th>Capacity</th>
                        <th>Air Conditioned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'db_config.php'; // Include your database configuration file
                    $query = "SELECT * FROM buses"; // Adjust the table name as per your database structure
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Access data for each bus record
                        $busId = $row['bus_id'];
                        $route = $row['route'];
                        $fare = $row['fare'];
                        $capacity = $row['capacity'];
                        $airConditioned = $row['air_conditioned'];

                        // Process or display the data as needed
                        // For example, you can echo the data in a table row
                        echo "<tr>";
                        echo "<td>" . $busId . "</td>";
                        echo "<td>" . $route . "</td>";
                        echo "<td>" . $fare . "</td>";
                        echo "<td>" . $capacity . "</td>";
                        echo "<td>" . ($airConditioned ? "Yes" : "No") . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; 2023 Bus Ticketing Service</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
