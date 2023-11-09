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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />

    <title>Manage Buses - Bus Ticketing System</title>
</head>

<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">

        </header>
        <main class="container my-5">
            <section id="manage-buses" class="mb-4 px-3">
                <div class="container text-center">
                    <h2>Manage Buses</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBusModal">Add Bus</button>
                </div>
            </section>
        </main>
    </div>

    <!--add Bus Modal -->
    <div class="modal fade" id="addBusModal" tabindex="-1" role="dialog" aria-labelledby="addBusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBusModalLabel">Add Bus</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="add_bus.php" method="post">


                        <div class="form-group">
                            <label for="plateNumber">Plate number:</label>
                            <input type="text" class="form-control" id="plateNumber" name="plateNumber" required>
                        </div>
                        <div class="form-group">
                            <label for="driverName">Bus driver name:</label>
                            <input type="text" class="form-control" id="driverName" name="driverName" required>
                        </div>
                        <div class="form-group">
                            <label for="contactNum">driver contact number:</label>
                            <input type="text" class="form-control" id="contactNum" name="contactNum" placeholder="+63----------" required>
                        </div>
                        <div class="form-group">
                            <label for="busPhoto">Bus Photo (max 5MB) (Optional):</label>
                            <input type="file" id="busPhoto" name="busPhoto" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="route">Route:</label>
                            <select class="form-control" id="route" name="route" required>
                                <option value="Going North">Going North</option>
                                <option value="Going South">Going South</option>
                            </select>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Bus ID</th>
                        <th>Route</th>
                        <th>Capacity</th>
                        <th>Air Conditioned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'db_config.php'; 
                    $query = "SELECT * FROM buses";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $busId = $row['bus_id'];
                        $route = $row['route'];
                        $capacity = $row['capacity'];
                        $airConditioned = $row['air_conditioned'];

                        echo "<tr>";
                        echo "<td>" . $busId . "</td>";
                        echo "<td>" . $route . "</td>";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>