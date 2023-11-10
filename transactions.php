<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: admin_login.php");
    exit();
}

include 'db_config.php';

$sql = "
SELECT transaction_code, ticket, bus_plate_number, student_id, stop, fare, booked_at, paid_at, user_token, ticket_expiration_timestamp FROM bookings WHERE status = 'paid'
ORDER BY paid_at DESC;
";

try {
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
        <main class="container my-5">
            <section id="admin-panel" class="mb-4 px-3">
                <div class="container text-center">

                    <div class="container mt-5">
                        <div class="row">
                            <div class="container text-center py-5">
                                <h2>Transaction History</h2>
                                <a href="admin_control_panel.php" class="btn btn-outline-primary btn-md">Control Panel</a>
                            </div>

                            <div class="input-group py-3">
                                <input type="text" class="form-control" placeholder="Search...">
                                <button class="btn btn-primary" type="button">Search</button>
                            </div>

                            <div class="col-lg-12">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Transaction code</th>
                                            <th scope="col">Ticket</th>
                                            <th scope="col">Bus Plate #</th>
                                            <th scope="col">Student ID / Name </th>
                                            <th scope="col">User token</th>
                                            <th scope="col">Stop</th>
                                            <th scope="col">Amount paid </th>
                                            <th scope="col">Booked on</th>
                                            <th scope="col">Paid on</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $row) : ?>
                                            <tr data-transaction-code="<?= $row['transaction_code'] ?>">
                                                <td class="transaction_code"><?= $row['transaction_code'] ?></td>
                                                <td class="ticket"><?= $row['ticket'] ?></td>
                                                <td class="bus_plate_number"><?= $row['bus_plate_number'] ?></td>
                                                <td class="student_id"><?= $row['student_id'] ?></td>
                                                <td class="user_token"><?= $row['user_token'] ?></td>
                                                <td class="stop"><?= $row['stop'] ?></td>
                                                <td class="fare"><?= $row['fare'] ?></td>
                                                <td class="booked_at"><?= $row['booked_at'] ?></td>
                                                <td class="paid_at"><?= $row['paid_at'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>


                            </div>

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