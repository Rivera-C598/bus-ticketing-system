<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: admin_login.php");
    exit();
}

include 'db_config.php';

date_default_timezone_set('Asia/Manila');
$currentTimestamp = date('Y-m-d H:i:s');

$sql = "
SELECT ticket, bus_plate_number, stop, student_id, fare, booked_at, status FROM bookings WHERE status = 'unpaid' && ticket_expiration_timestamp > :currentTimestamp
ORDER BY booked_at DESC;
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':currentTimestamp' => $currentTimestamp]);
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
    <style>
        .table tbody tr {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Control Panel</h1>
            </div>
        </header>
        <main class="container my-5">
            <section id="admin-panel" class="mb-4 px-3">
                <div class="container text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="manage_buses.php" class="btn btn-primary btn-lg btn-block">
                                Manage Buses
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="transactions.php" class="btn btn-primary btn-lg btn-block">Transaction history</a>

                            <a href="students.php" class="btn btn-primary btn-lg btn-block">Student reference</a>

                        </div>
                    </div>

                    <!-- Hero section for the table and CRUD controls -->
                    <div class="container mt-5">
                        <div class="row">
                            <h2 class="text-center">Bookings</h2>

                            <!-- Search bar -->
                            <div class="input-group py-3">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            </div>

                            <div class="col-lg-12">

                                <!-- Bootstrap table -->
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Ticket</th>
                                            <th scope="col">Bus plate number</th>
                                            <th scope="col">Stop </th>
                                            <th scope="col">Student ID</th>
                                            <th scope="col">Fare </th>
                                            <th scope="col">Booked at</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $row) : ?>
                                            <tr data-ticket-code="<?= $row['ticket'] ?>">
                                                <td class="ticket"><?= $row['ticket'] ?></td>
                                                <td class="bus_plate_number"><?= $row['bus_plate_number'] ?></td>
                                                <td class="stop"><?= $row['stop'] ?></td>
                                                <td class="student_id"><?= $row['student_id'] ?></td>
                                                <td class="fare"><?= $row['fare'] ?></td>
                                                <td class="booked_at"><?= $row['booked_at'] ?></td>
                                                <td class="status"><?= $row['status'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>


                            </div>

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

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Booking Details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Display the row's information here -->
                    <p><strong>Ticket: </strong><span id="modalTicket"></span></p>
                    <p><strong>Bus Plate Number: </strong><span id="modalBusPlateNumber"></span></p>
                    <p><strong>Stop: </strong><span id="modalStop"></span></p>
                    <p><strong>Student ID: </strong><span id="modalStudentID"></span></p>
                    <p><strong>Fare: </strong><span id="modalFare"></span></p>
                    <p><strong>Booked At: </strong><span id="modalBookedAt"></span></p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" id="changeStatusToPaid">Change Status to Paid</button>
                </div>
            </div>
        </div>
    </div>



    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; 2023 Bus Ticketing Service</p>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            //when table row is selected
            $('table tbody tr').on('click', function() {
                //get data from the row
                var ticket = $(this).find('.ticket').text();
                var busPlateNumber = $(this).find('.bus_plate_number').text();
                var stop = $(this).find('.stop').text();
                var studentID = $(this).find('.student_id').text();
                var fare = $(this).find('.fare').text();
                var bookedAt = $(this).find('.booked_at').text();

                //populate modal
                $('#modalTicket').text(ticket);
                $('#modalBusPlateNumber').text(busPlateNumber);
                $('#modalStop').text(stop);
                $('#modalStudentID').text(studentID);
                $('#modalFare').text(fare);
                $('#modalBookedAt').text(bookedAt);

                $('#bookingModal').modal('show');
            });

            $('#changeStatusToPaid').on('click', function() {

                var ticket = $('#modalTicket').text();
                var busPlateNumber = $('#modalBusPlateNumber').text();

                $.ajax({
                    type: 'POST',
                    url: 'update_status_and_move.php',
                    data: {
                        ticket: ticket,
                        busPlateNumber: busPlateNumber
                    },
                    success: function(response) {
                        if (response === 'success') {
                            $('#bookingModal').modal('hide');
                            $('tr[data-ticket-code="' + ticket + '"]').remove();
                        } else if (response === 'error: Bus is full') {
                            alert('Bus is already full. Cannot update status.');
                        } else {
                            alert('Status update failed. Bus unavailable.');
                        }
                    }
                });
            });

            

            


            
        });
    </script>


</body>

</html>