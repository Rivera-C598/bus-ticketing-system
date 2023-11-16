<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../admin_login.php");
    exit();
}

include '../../../../database_config/db_config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 15; //row count
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "
    SELECT transaction_code, ticket, bus_plate_number, student_id, stop, fare, booked_at, paid_at, user_token, ticket_expiration_timestamp FROM bookings 
    WHERE status = 'paid' AND (transaction_code LIKE '%$searchTerm%' OR ticket LIKE '%$searchTerm%' OR bus_plate_number LIKE '%$searchTerm%' OR student_id LIKE '%$searchTerm%')
    ORDER BY paid_at DESC
    LIMIT $limit OFFSET $offset;
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
    <link rel="stylesheet" href="../../../../css/styles.css">
    <style>
        #title {
            border-bottom: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
    </style>
    <title>Admin Control Panel - Transactions history</title>
</head>

<body>
    <div class="wrapper">
        <div class="container text-center py-3" id="title">
            <h2>Transaction History</h2>
            <a href="../mirage/admin_control_panel.php" class="btn btn-outline-primary btn-md">Control Panel</a>
        </div>
        <main class="container">
            <section id="admin-panel" class="mb-4 px-3">


                <div class="container">
                    <div class="row">


                        <div class="container mt-5">
                            <form method="GET" action="" class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search..." name="search" value="<?= $searchTerm ?>">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">



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

                            <div class="d-flex justify-content-center">
                                <?php
                                $totalRecords = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'paid' AND (transaction_code LIKE '%$searchTerm%' OR ticket LIKE '%$searchTerm%' OR bus_plate_number LIKE '%$searchTerm%' OR student_id LIKE '%$searchTerm%')")->fetchColumn();
                                $totalPages = ceil($totalRecords / $limit);

                                echo '<ul class="pagination">';
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '&search=' . $searchTerm . '">' . $i . '</a></li>';
                                }
                                echo '</ul>';
                                ?>

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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function updateTable() {
            $.ajax({
                url: 'update_transactions_table.php',
                success: function(data) {
                    $('#yourTableID').html(data);
                }
            });
        }

        setInterval(updateTable, 5000); //update every 5 secuz
    </script>
</body>

</html>