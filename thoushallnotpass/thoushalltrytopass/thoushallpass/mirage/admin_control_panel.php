<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../admin_login.php");
    exit();
} else {
    $adminId = $_SESSION['admin_id'];
    $adminName = $_SESSION['admin_name'];
}


include '../../../../database_config/db_config.php';
include '../../../../time/time_conf.php';
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
    <link rel="stylesheet" href="../../../../css/styles.css">
    <title>Admin Control Panel - Bus Ticketing System</title>
    <style>
        .wrapper {
            position: relative;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: url('../../../../img_assets/web-bg.jpg') center no-repeat;
            background-size: cover;
        }

        #table-container,
        #control-buttons {
            background: rgba(255, 255, 255, 0.19);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        #searchInput {
            background: rgba(255, 255, 255, 0.01);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .table {
            background-color: white;
        }

        .table tbody tr {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .badge.text-bg-secondary {
            background-color: orange;
            color: black;
        }

        #summaryModal .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }

        #control-buttons {
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
        }

        #admin-panel {
            border-top: 3px solid #ccc;
        }

        .small-image {
            max-width: 50%;
            height: auto;

        }
    </style>
</head>

<body>


    <!-- Content -->
    <div class="wrapper">
        <header class="bg-dark text-white text-center py-3">
            <div class="container">
                <h1>Welcome, <span id="currentAdmin"><?php echo $adminName ?></span></h1>
            </div>
        </header>
        <main class="container">
            <section id="admin-panel" class="mb-5">

                <div class="container text-center">
                    <div class="row" id="control-buttons">
                        <div class="col-md-6 col-lg-5 mb-3">
                            <a class="btn btn-success btn-lg btn-block" href="#" data-bs-toggle="modal" data-bs-target="#ticketModal">Create Bus Tickets</a>
                        </div>
                        <div class="col-md-6 col-lg-5 mb-3">
                            <div class="btn-group" role="group">
                                <a href="../manage_bus/manage_buses.php" class="btn btn-outline-primary btn-md">Manage Buses</a>
                                <a href="../view_transactions/transactions.php" class="btn btn-outline-primary btn-md">Transaction history</a>
                                <a href="../manage_students/students.php" class="btn btn-outline-primary btn-md">Student reference</a>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-2">
                            <a href="admin_logout.php" class="btn btn-outline-danger btn-md">Log out</a>
                        </div>
                    </div>
                    <!-- tobol container -->
                    <div class="container mt-5" id="table-container">
                        <div class="row">
                            <h2 class="text-center">Bookings</h2>
                            <!-- search -->
                            <div class="input-group py-3">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            </div>
                            <div class="table-responsive">
                                <!-- table -->
                                <table class="table table-bordered" id="bookingsTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Ticket</th>
                                            <th scope="col">Bus plate number</th>
                                            <th scope="col">Stop</th>
                                            <th scope="col">Student ID</th>
                                            <th scope="col">Fare</th>
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
                </div>
            </section>
        </main>
    </div>


    <!-- ticket modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Generate Tickets</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="route">Route:</label>
                        <select class="form-control" id="route" name="route">
                            <option value="ChooseRoute" disabled selected>Choose a route</option>
                            <option value="North">North</option>
                            <option value="South">South</option>
                        </select>
                    </div>

                    <!-- chain reaction form -->
                    <div id="dynamicForm">
                        <!-- this will be populated latur -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id='ticketModalCloseBtn'>Close</button>
                </div>
            </div>
        </div>
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
                    <!-- display bus info -->
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

    <!-- summary modal -->
    <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="summaryModalLabel">Ticket Summary</h5>
                </div>
                <div class="modal-body" id="summaryBody">
                    <!-- latur again -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="summaryModalCloseBtn">Hide</button>
                    <button type="button" class="btn btn-primary" id="confirmPrintButton">Confirm Payment & Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- success modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    <p class="text-center">Success</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="okButton">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- bus full or unavailable modal -->
    <div class="modal fade" id="busFullOrUnavailableModal" tabindex="-1" role="dialog" aria-labelledby="busFullOrUnavailableModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="busFullOrUnavailableModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    <p class="text-center">Bus full or unavailable.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="okButton">Ok</button>
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
            //put the search functionality to the input field
            $('#searchInput').on('keyup', function() {
                updateTable();
            });

            function updateTable() {
                var searchTerm = $('#searchInput').val();
                $.ajax({
                    type: 'POST',
                    url: '../get_files/update_table.php',
                    data: {
                        searchTerm: searchTerm
                    },
                    success: function(data) {
                        $('#bookingsTable tbody').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: " + status, error);

                    }
                });
            }

            //find row
            $('#bookingsTable tbody').on('click', 'tr', function() {
                //get data from selected row
                var ticket = $(this).find('.ticket').text();
                var busPlateNumber = $(this).find('.bus_plate_number').text();
                var stop = $(this).find('.stop').text();
                var studentID = $(this).find('.student_id').text();
                var fare = $(this).find('.fare').text();
                var bookedAt = $(this).find('.booked_at').text();

                //transfer data to modal
                $('#modalTicket').text(ticket);
                $('#modalBusPlateNumber').text(busPlateNumber);
                $('#modalStop').text(stop);
                $('#modalStudentID').text(studentID);
                $('#modalFare').text(fare);
                $('#modalBookedAt').text(bookedAt);

                $('#bookingModal').modal('show');
            });

            //update table every 5 sekus
            setInterval(updateTable, 5000);


            $('#changeStatusToPaid').on('click', function() {

                var ticket = $('#modalTicket').text();
                var busPlateNumber = $('#modalBusPlateNumber').text();
                var stop = $('#modalStop').text();
                var studentID = $('#modalStudentID').text();
                var fare = $('#modalFare').text();


                $.ajax({
                    type: 'POST',
                    url: '../get_scripts/update_status_and_move.php',
                    data: {
                        ticket: ticket,
                        busPlateNumber: busPlateNumber
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            var transactionCode = response.transactionCode;

                            printReceipt({
                                ticket: ticket,
                                busPlateNumber: busPlateNumber,
                                stop: stop,
                                studentID: studentID,
                                fare: fare,
                                todaysDate: getFormattedDateTime(),
                                adminName: $('#currentAdmin').text(),
                                transactionCode: transactionCode
                            });

                            $('#bookingModal').modal('hide');
                            $('tr[data-ticket-code="' + ticket + '"]').remove();

                        } else if (response.status === 'error: Bus not found') {
                            $('#bookingModal').modal('hide');
                            $('#busFullOrUnavailableModal').modal('show');
                        } else {
                            alert('Unexpected error occured');
                        }
                    }
                });
            });

            function printReceipt(data) {
                var printWindow = window.open('', '_blank');

                var receiptContent = `
                    <div>
                        <p><strong>--------------- CTU-Danao [ Bus Ride Ticket ] ------------------</strong></p>
                        <p><strong>Bus: [${data.busPlateNumber}] </strong>| <strong>Ticket# [${data.ticket}]</strong></p>
                        <p><strong>Student ID:</strong> ${data.studentID} | <strong>Stop:</strong> ${data.stop} | Php${data.fare}.00</p>
                        <p><strong>Receipt#:</strong> ${data.transactionCode} | <strong>Date:</strong> ${data.todaysDate}</p>
                        <p><strong>Cashier: </strong> ${data.adminName}</p>
                        <p><strong>Thanks for riding with us! :D</strong></p>
                        <p>--------------------------------online-----------------------------------</p>
                    </div>
                `;

                printWindow.document.write(receiptContent);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
            }

            function getFormattedDateTime() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                var hh = today.getHours();
                var min = String(today.getMinutes()).padStart(2, '0');
                var sec = String(today.getSeconds()).padStart(2, '0');

                var ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12;
                hh = hh ? hh : 12;

                return mm + '/' + dd + '/' + yyyy + ' ' + hh + ':' + min + ':' + sec + ' ' + ampm;
            }

        });
    </script>


    <script>
        $(document).ready(function() {
            var routeDropdown = $('#route');

            var form = $('#dynamicForm');
            form.empty();

            var busAvailableSlots;
            var isStudentVerificationOn = true;
            var updateSlotsInterval;
            //route selection first
            $('#route').change(function() {
                isStudentVerificationOn = true;
                routeSelection = $(this).val();
                clearInterval(updateSlotsInterval);

                if (routeSelection === 'ChooseRoute') {
                    form.hide();
                } else if (routeSelection === 'North') {
                    form.show();
                    form.html(
                        `<div class="form-group">
                    <label for="busSelection">Select Bus (North):</label>
                    <select class="form-control" id="busSelection" name="busSelection">
                       
                    </select>
                    </div>
                
                    <!-- Show selected bus details -->
                
                    <div id="busDetails">
                    <!-- This content will be replaced based on the bus selection -->
                    </div>`
                    );
                    //when route is changed, empty previous shit
                    var busDropdown = $('#busSelection');
                    var busDetails = $('#busDetails');
                    busDetails.empty();
                    busDropdown.empty();


                    $.ajax({
                        url: '../get_scripts/get_available_buses.php',
                        method: 'GET',
                        data: {
                            route: 'Going North' //paramter
                        },
                        success: function(data) {
                            busDropdown.empty();
                            busDropdown.append(
                                $('<option>', {
                                    value: '',
                                    text: 'Choose a bus',
                                    disabled: true,
                                    selected: true
                                })
                            );
                            data.forEach(function(bus) {
                                busDropdown.append(
                                    $('<option>', {
                                        value: bus.bus_id,
                                        text: bus.plate_number
                                    })
                                );
                            });

                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error: ' + status, error);
                        }
                    });

                    var busDetailsContainer;
                    var busAirconditioned;
                    var busId;
                    var busPlateNumber;

                    var schoolIdStopContainer = $('<div>', {
                        id: 'schoolIdStopContainer',
                        class: 'form-group',
                        style: 'border: solid 1px #ccc; padding: 8px'
                    });

                    $('#busSelection').change(function() {
                        isStudentVerificationOn = true;

                        var schoolIdStopContainer = $('#schoolIdStopContainer');

                        //clear container
                        schoolIdStopContainer.empty();
                        clearInterval(updateSlotsInterval);


                        busId = $(this).val();

                        $.ajax({
                            url: '../get_scripts/get_bus_combo.php',
                            method: 'GET',
                            data: {
                                busId: busId
                            },
                            success: function(busData) {

                                //clear previous 
                                busDetails.empty();
                                busDetails.show();

                                //create new container for bus details
                                busDetailsContainer = $('<div>', {
                                    style: 'margin-top: 10px; border-top: 1px solid #ccc;'
                                });

                                //then display bus info and append to busDetailsContainer div
                                busDetailsContainer.append($('<h4>', {
                                    text: 'Bus Details',
                                    class: 'text-center'
                                }));
                                busDetailsContainer.append($('<img>', {
                                    src: busData.busPhoto,
                                    alt: 'Bus Photo',
                                    class: 'img-fluid mx-auto d-block small-image'
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    text: 'Plate Number: ' + busData.plate_number,
                                    id: 'busPlateNumber'
                                }));
                                busPlateNumber = busData.plate_number;
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',

                                    text: 'Driver Name: ' + busData.bus_driver_name
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAirconditioned',
                                    text: 'Airconditioned: ' + (busData.air_conditioned ? 'Yes' : 'No')
                                }));

                                busAirconditioned = busData.air_conditioned;

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAvailableSlots',
                                    text: 'Loading...'
                                }));

                                var ticketCountContainer = $('<div>', {
                                    class: 'input-group mb-3',
                                    id: 'ticketCountContainer'

                                });

                                var ticketCountInput = $('<input>', {
                                    type: 'number',
                                    id: 'ticketCountInput',
                                    name: 'ticketCountInput',
                                    placeholder: 'Enter ticket quantity',
                                    min: 1,
                                    class: 'form-control'

                                });

                                var inputAppendDiv = $('<div>', {
                                    class: 'input-group-append'
                                });

                                var startLoopBtn = $('<btn>', {
                                    class: 'btn btn-success',
                                    text: 'Start',
                                    id: 'startLoopBtn'
                                });

                                busAvailableSlots = busData.available_slots;
                                updateSlotsInterval = setInterval(updateAvailableSlots, 5000);

                                ticketCountContainer.append(ticketCountInput);
                                ticketCountContainer.append(inputAppendDiv);
                                inputAppendDiv.append(startLoopBtn);

                                //append bus details to busDetails div
                                busDetails.append(busDetailsContainer);
                                busDetailsContainer.append(ticketCountContainer);
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX error: ' + status, error);

                            }
                        });

                        function updateAvailableSlots() {
                            $.ajax({
                                url: '../../../../rev/rev_get_files/get_available_slots.php',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    busId: busId
                                },
                                success: function(response) {
                                    if (response.hasOwnProperty('availableSlots')) {
                                        $('#busAvailableSlots').text('Available Slots: ' + response.availableSlots);
                                    }
                                },
                                error: function() {
                                    $('#availableSlots').text('N/A');
                                }
                            });
                        }



                    });






                    var schoolIdStopContainer = $('<div>', {
                        id: 'schoolIdStopContainer',
                        class: 'form-group',
                        style: 'border: solid 1px #ccc; padding: 8px'
                    });

                    var ticketForm = $('<form>', {
                        id: 'ticketForm',
                    });


                    var labelAndToggleVerificationBtnContainer = $('<div>', {
                        id: 'labelAndToggleVerificationBtnContainer',
                        class: 'form-group row g-2',
                        style: 'margin-bottom: 2px'
                    });

                    var schoolIdLabelDiv = $('<div>', {
                        class: 'col-sm',

                    });

                    var toggleVerificationDiv = $('<div>', {
                        class: 'col-auto'
                    });

                    var toggleVerificationBtn = $('<button>', {
                        id: 'toggleStudentVerification',
                        class: 'btn btn-success btn-sm',
                        text: 'Verification On',
                        type: 'button'
                    });

                    var splitFareDiv = $('<div>', {
                        id: 'splitFareDiv',
                        class: 'input-group',
                        style: 'margin-top: 5px; margin-bottom: 5px;'
                    });


                    var confirmAndCancelBtnDiv = $('<div>', {
                        class: 'justify-content-center d-flex my-2'

                    });

                    var confirmBtn = $('<button>', {
                        class: 'btn btn-primary',
                        text: 'Confirm',
                        type: 'button'
                    });

                    var cancelBtn = $('<button>', {
                        id: 'cancelButton',
                        class: 'btn btn-outline-danger',
                        text: 'Cancel',
                        type: 'button'
                    });

                    var fareDictionary = {
                        'Danao': 10.00,
                        'Carmen': 20.00,
                        'Catmon': 30.00,
                        'Sogod': 40.00
                    };

                    //modify e fare dictionary if bus is tugnaw
                    /*if (busIsAirConditioned) {
                        fareDictionary = {
                            'Danao': 15.00,
                            'Carmen': 25.00,
                            'Catmon': 35.00,
                            'Sogod': 45.00
                        };
                    }*/

                    var ticketData = [];
                    var currentTicketIndex = 0;

                    $(document).on('click', '#startLoopBtn', function() {

                        var emptySchoolIdStopContainer = $('#schoolIdStopContainer');
                        var available_seats_error = $('#available_seats_error');

                        //show ticket form
                        var ticketCount = $('#ticketCountInput').val();
                        ticketCount = ticketCount.replace(/^0+/, '');
                        ticketCount = ticketCount.split('.')[0];

                        $('#confirmButton').prop('disabled', true);
                        $('#toggleStudentVerification').removeClass('btn-danger').addClass('btn-success').text('Verification On');
                        isStudentVerificationOn = true;
                        currentTicketIndex = 0;
                        ticketData = [];

                        confirmBtn.empty();
                        schoolIdLabelDiv.empty();
                        splitFareDiv.empty();
                        ticketForm.empty();



                        if (ticketCount <= busAvailableSlots && ticketCount > 0) {

                            if (currentTicketIndex < ticketCount) {

                                //show form if valid ticket count
                                ticketCountContainer = $('#ticketCountContainer');
                                ticketCountContainer.hide();
                                available_seats_error.hide();
                                emptySchoolIdStopContainer.empty();

                                ticketCountContainer.append(schoolIdStopContainer);
                                schoolIdStopContainer.show();

                                schoolIdLabelDiv.append($('<label>', {
                                    for: 'schoolIdInput',
                                    text: 'School Id:'
                                }));

                                labelAndToggleVerificationBtnContainer.append(schoolIdLabelDiv)
                                toggleVerificationDiv.append(toggleVerificationBtn);
                                labelAndToggleVerificationBtnContainer.append(toggleVerificationDiv)



                                schoolIdStopContainer.append(ticketForm);


                                ticketForm.append(labelAndToggleVerificationBtnContainer);

                                var schoolIdInput = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'schoolIdInput',
                                    name: 'schoolIdInput',
                                    placeholder: 'Enter school ID',
                                    required: true
                                });

                                var studentExistingBookingStatus = $('<input>', {
                                    type: 'text',
                                    id: 'studentExistingBookingStatus',
                                    name: 'studentExistingBookingStatus',
                                    hidden: true
                                })

                                var hasPassedCooldownStatus = $('<input>', {
                                    type: 'text',
                                    hidden: true,
                                    id: 'hasPassedCooldownStatus',
                                    name: 'hasPassedCooldownStatus'
                                })

                                ticketForm.append(schoolIdInput);
                                ticketForm.append($('<p>', {
                                    id: 'hasNotPassedCooldownPeriodErrorMessage',
                                    text: '',
                                    style: 'color: red;'
                                }))
                                ticketForm.append(studentExistingBookingStatus);
                                ticketForm.append(hasPassedCooldownStatus);

                                ticketForm.append($('<label>', {
                                    for: 'stopSelection',
                                    text: 'Stop:'
                                }));

                                var stopSelection = $('<select>', {
                                    id: 'stopSelection',
                                    name: 'stopSelection',
                                    class: 'form-control',
                                    required: true
                                })

                                stopSelection.append($('<option>', {
                                    value: '',
                                    text: 'Choose Stop',
                                    selected: true,
                                    disabled: true
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Danao',
                                    text: 'Danao'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Carmen',
                                    text: 'Carmen'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Catmon',
                                    text: 'Catmon'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Sogod',
                                    text: 'Sogod'
                                }));

                                ticketForm.append(stopSelection);

                                splitFareDiv.append($('<span>', {
                                    class: 'input-group-text',
                                    text: 'Fare:'

                                }));

                                var fare = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'fare',
                                    name: 'fare',
                                    readonly: true
                                });

                                splitFareDiv.append(fare);


                                /*splitFareDiv.append($('<span>', {
                                    class: 'input-group-text',
                                    text: 'Total Fare:'
                                }));*/
                                var totalFare = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'totalFare',
                                    name: 'totalFare',
                                    readonly: true,
                                    disabled: true
                                });



                                ticketForm.append(splitFareDiv);

                                confirmBtn = $('<button>', {
                                    class: 'btn btn-primary',
                                    text: 'Confirm',
                                    type: 'button',
                                    id: 'confirmButton',
                                    disabled: true
                                });

                                confirmBtn.append($('<span>', {
                                    class: 'badge text-bg-secondary',
                                    text: ticketCount,
                                    id: 'ticketCountBadge'
                                }))

                                confirmAndCancelBtnDiv.append(confirmBtn, cancelBtn);


                                ticketForm.append(confirmAndCancelBtnDiv);

                                busDetailsContainer.append(schoolIdStopContainer);



                                if (isStudentVerificationOn) {
                                    $('#schoolIdInput').on('keyup', function() {
                                        var schoolId = $('#schoolIdInput').val();
                                        //check shoolid on keyup
                                        $.ajax({
                                            url: '../get_scripts/verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = response;
                                                if (responseData.schoolIdExists) {

                                                    //if school id exissts
                                                    if (responseData.lastRequestStatus) {

                                                        if (responseData.passedCooldownPeriod) {
                                                            //good to go
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                            $('#studentExistingBookingStatus').val('true');
                                                            $('#hasPassedCooldownStatus').val('true');
                                                            $('#confirmButton').prop('disabled', false);
                                                        } else {
                                                            //if student has not yet passed last request cooldown status
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                            // disable confirm button
                                                            $('#confirmButton').prop('disabled', true);
                                                        }

                                                    } else {
                                                        //theres no last reqesut, set to false
                                                        $('#studentExistingBookingStatus').val('false');
                                                        //enable button cuz this must be student first tiem request
                                                        $('#confirmButton').prop('disabled', false);
                                                    }
                                                } else {
                                                    //school id not exist
                                                    $('#studentExistingBookingStatus').val('');
                                                    //so disable
                                                    $('#confirmButton').prop('disabled', true);
                                                }
                                            },
                                            error: function() {}
                                        });

                                    });
                                }

                                $('#toggleStudentVerification').on('click', function() {
                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                    var schoolIdLabel = $('label[for="schoolIdInput"]');
                                    var schoolIdInput = $('#schoolIdInput');

                                    if (isStudentVerificationOn) {
                                        //dont give a shit mode
                                        schoolIdLabel.text('Passenger Name:');
                                        schoolIdInput.attr('placeholder', 'Enter passenger name');
                                        isStudentVerificationOn = false;

                                        $('#schoolIdInput').off('keyup');

                                        $('#confirmButton').prop('disabled', false);

                                        $(this).removeClass('btn-success').addClass('btn-danger').text('Verification Off');
                                    } else {
                                        //school Id mode
                                        schoolIdLabel.text('School Id:');
                                        schoolIdInput.attr('placeholder', 'Enter school ID');
                                        isStudentVerificationOn = true;
                                        $('#confirmButton').prop('disabled', true);

                                        var schoolId = $('#schoolIdInput').val();
                                        $.ajax({
                                            url: '../get_scripts/verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = response;
                                                if (responseData.schoolIdExists) {

                                                    // School ID exists
                                                    if (responseData.lastRequestStatus) {

                                                        if (responseData.passedCooldownPeriod) {
                                                            //good to go
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                            $('#studentExistingBookingStatus').val('true');
                                                            $('#hasPassedCooldownStatus').val('true');
                                                            $('#confirmButton').prop('disabled', false);
                                                        } else {
                                                            //if student has not yet passed last request cooldown status
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                            // disable confirm button
                                                            $('#confirmButton').prop('disabled', true);
                                                        }

                                                    } else {
                                                        $('#studentExistingBookingStatus').val('false');
                                                        $('#confirmButton').prop('disabled', false);
                                                    }
                                                } else {
                                                    $('#studentExistingBookingStatus').val('');
                                                    $('#confirmButton').prop('disabled', true);
                                                }
                                            },
                                            error: function() {

                                            }
                                        });

                                        //set up keyup event handler again when verification is turned on
                                        $('#schoolIdInput').on('keyup', function() {
                                            var schoolId = $('#schoolIdInput').val();
                                            $.ajax({
                                                url: '../get_scripts/verify_schoolId.php',
                                                type: 'POST',
                                                data: {
                                                    schoolId: schoolId
                                                },
                                                success: function(response) {
                                                    var responseData = response;
                                                    if (responseData.schoolIdExists) {

                                                        if (responseData.lastRequestStatus) {

                                                            if (responseData.passedCooldownPeriod) {
                                                                //good to go
                                                                $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                                $('#studentExistingBookingStatus').val('true');
                                                                $('#hasPassedCooldownStatus').val('true');
                                                                $('#confirmButton').prop('disabled', false);
                                                            } else {
                                                                //if student has not yet passed last request cooldown status
                                                                $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                                // disable confirm button
                                                                $('#confirmButton').prop('disabled', true);
                                                            }

                                                        } else {
                                                            $('#studentExistingBookingStatus').val('false');
                                                            $('#confirmButton').prop('disabled', false);
                                                        }
                                                    } else {
                                                        $('#studentExistingBookingStatus').val('');
                                                        $('#confirmButton').prop('disabled', true);
                                                    }
                                                },
                                                error: function() {}
                                            });
                                        });

                                        $(this).removeClass('btn-danger').addClass('btn-success').text('Verification On');
                                    }


                                });


                                $('#stopSelection').on('change', function() {
                                    var selectedStop = $(this).val();
                                    var fareInput = $('#fare');

                                    console.log(busAirconditioned);
                                    //check fare input if fare input in fare dicktionary
                                    var baseFare = fareDictionary[selectedStop] || 0.00; // Default to 0.00 if fare not found

                                    //if air conditioned add 10
                                    var finalFare = busAirconditioned ? baseFare + 10.00 : baseFare;

                                    //update fare input
                                    fareInput.val(finalFare.toFixed(2));
                                });

                                $('#cancelButton').on('click', function() {

                                    currentTicketIndex = 0;
                                    ticketData = [];
                                    schoolIdStopContainer.hide();
                                    $('#ticketCountInput').val('');
                                    confirmAndCancelBtnDiv.empty();
                                    ticketCountContainer.show();

                                })


                                //confirm button clicked
                                $('#confirmButton').on('click', function() {
                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');

                                    var schoolIdInput = $('#schoolIdInput');
                                    var schoolId = $('#schoolIdInput').val();
                                    var stop = $('#stopSelection').val();
                                    var fare = $('#fare').val();
                                    var lastBookingStatus = $('#studentExistingBookingStatus').val();
                                    console.log(lastBookingStatus);
                                    var ticketCount = $('#ticketCountInput').val();
                                    const ticketCode = generateUniqueTicketCode();

                                    if (schoolId.trim() !== '' && stop != null) {
                                        //dpt di magbalik2 ang id or name gi enter
                                        if (!isStudentIdRepeated(schoolId)) {
                                            //collect data, store in array first
                                            ticketData.push({
                                                ticketCode: ticketCode,
                                                busPlateNumber: busPlateNumber,
                                                schoolId: schoolId,
                                                stop: stop,
                                                fare: fare,
                                                isStudentVerified: isStudentVerificationOn,
                                                lastBookingStatus: lastBookingStatus
                                            });

                                            //increment
                                            currentTicketIndex++;

                                            //clear, get ready for next iteration
                                            schoolIdInput.val('');
                                            $('#stopSelection').val('');
                                            $('#fare').val('');

                                            if (currentTicketIndex >= ticketCount) {
                                                if (ticketData.length > 0) {
                                                    //pupulate summry content
                                                    var summaryContent = "";
                                                    for (var i = 0; i < ticketData.length; i++) {
                                                        var ticket = ticketData[i];
                                                        summaryContent += '<p><strong>Ticket Code:</strong> ' + ticket.ticketCode + '</p>';
                                                        summaryContent += '<p><strong>Bus Plate number:</strong> ' + ticket.busPlateNumber + '</p>';
                                                        summaryContent += '<p><strong>School ID / Name:</strong> ' + ticket.schoolId + '</p>';
                                                        summaryContent += '<p><strong>Stop:</strong> ' + ticket.stop + '</p>';
                                                        summaryContent += '<p><strong>Fare:</strong> ' + ticket.fare + '</p>';
                                                        summaryContent += '<hr>';
                                                    }

                                                    //set summary content to summary bodeh
                                                    $('#summaryBody').html(summaryContent);

                                                    //show modal
                                                    $('#ticketModal').modal('hide');
                                                    $('#summaryModal').modal('show');
                                                } else {
                                                    console.log('No ticket data to display.');
                                                }


                                            } else {
                                                $('#ticketCountBadge').text(ticketCount - currentTicketIndex);
                                            }
                                        } else {
                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('Student ID already entered.');
                                        }
                                    } else {
                                        $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                        $('#hasNotPassedCooldownPeriodErrorMessage').text('School ID or Stop cannot be empty.');
                                    }
                                });

                                //check if the student ID is repeated
                                function isStudentIdRepeated(newStudentId) {
                                    return ticketData.some(ticket => ticket.schoolId === newStudentId);
                                }

                                function generateUniqueTicketCode() {
                                    const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                    const codeLength = 8;

                                    let ticketCode;
                                    let codeExistsInTickets;

                                    do {
                                        ticketCode = '';
                                        for (let i = 0; i < codeLength; i++) {
                                            ticketCode += characters.charAt(Math.floor(Math.random() * characters.length));
                                        }

                                        codeExistsInTickets = checkCodeInTickets(ticketCode);
                                    } while (codeExistsInTickets);

                                    return ticketCode;
                                }

                                function checkCodeInTickets(code) {
                                    //check if ticketcode in my array
                                    return ticketData.some(ticket => ticket.ticketCode === code);
                                }


                                $('#confirmPrintButton').on('click', function() {
                                    if (ticketData.length > 0) {
                                        $.ajax({
                                            url: 'admin_process_tickets.php',
                                            type: 'POST',
                                            data: {
                                                ticketData: JSON.stringify(ticketData)
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                var tickets = response.ticketData;
                                                console.log(response);

                                                if (tickets && tickets.length > 0) {
                                                    var printWindow = window.open('', '_blank');

                                                    var allTicketsHtml = tickets.map(function(ticket) {
                                                        return getTicketHtml(ticket, getFormattedDateTime());
                                                    }).join('');

                                                    printWindow.document.write(allTicketsHtml);
                                                    printWindow.document.close();
                                                    printWindow.print();
                                                    printWindow.close();

                                                    currentTicketIndex = 0;
                                                    ticketData = [];
                                                    schoolIdStopContainer.hide();
                                                    ticketCountContainer.show();
                                                    $('#ticketCountInput').val('');
                                                    $('#studentExistingBookingStatus').val('');
                                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                    $('#summaryModal').modal('hide');
                                                    location.reload();
                                                } else {
                                                    console.error('No ticket data received');
                                                }
                                            },
                                            error: function(error) {
                                                console.error(error);
                                            }
                                        });
                                    } else {
                                        console.log('No ticket data to confirm and print.');
                                        $('#summaryModal').modal('hide');
                                    }
                                });

                                function getTicketHtml(ticket, todaysDate) {
                                    var ticketHtml = `
                                    <div class="ticket-container">
                                        <p><strong>---------------- CTU-Danao [ Bus Ride Ticket ] ----------------</strong></p>
                                        <p><strong>Bus: [${ticket.busPlateNumber}] </strong>| <strong>Ticket# [${ticket.ticketCode}]</strong></p>
                                        <p><strong>Student ID:</strong> ${ticket.schoolId} | <strong>Stop:</strong> ${ticket.stop} | Php${ticket.fare}</p>
                                        <p><strong>Receipt#:</strong> ${ticket.transactionCode} | <strong>Date:</strong> ${todaysDate}</p>
                                        <p><strong>Cashier: </strong> <?php echo $adminName ?></p>
                                        <p><strong>Thanks for riding with us! :D</strong></p>
                                        <p>------------------------------- cashier ---------------------------------</p>
                                    </div>
    `;
                                    return ticketHtml;
                                }


                                function getFormattedDateTime() {
                                    var today = new Date();
                                    var dd = String(today.getDate()).padStart(2, '0');
                                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                                    var yyyy = today.getFullYear();
                                    var hh = today.getHours();
                                    var min = String(today.getMinutes()).padStart(2, '0');
                                    var sec = String(today.getSeconds()).padStart(2, '0');

                                    var ampm = hh >= 12 ? 'PM' : 'AM';
                                    hh = hh % 12;
                                    hh = hh ? hh : 12;

                                    return mm + '/' + dd + '/' + yyyy + ' ' + hh + ':' + min + ':' + sec + ' ' + ampm;
                                }


                            }

                        } else {

                            if (ticketCount <= 0) {
                                available_seats_error.hide();
                                emptySchoolIdStopContainer.empty();
                            } else {
                                emptySchoolIdStopContainer.empty();

                                available_seats_error.show();

                                busDetailsContainer.append($('<p>', {
                                    id: 'available_seats_error',
                                    color: 'red',
                                }).css('color', 'red'));

                                var slot_error = $('#available_seats_error');

                                slot_error.text("Error: that's more than the available slots.");
                            }
                        }

                    });


                    //IF SOUTH -------------------------------------------------------------------------------------------------------------------------------------------------------------
                } else if (routeSelection === 'South') {
                    form.show();
                    form.html(
                        `<div class="form-group">
                    <label for="busSelection">Select Bus (South):</label>
                    <select class="form-control" id="busSelection" name="busSelection">
                       
                    </select>
                    </div>
                
                    <!-- Show selected bus details -->
                
                    <div id="busDetails">
                    <!-- This content will be replaced based on the bus selection -->
                    </div>`
                    );
                    //when route is changed, empty previous shit
                    var busDropdown = $('#busSelection');
                    var busDetails = $('#busDetails');
                    busDetails.empty();
                    busDropdown.empty();


                    $.ajax({
                        url: '../get_scripts/get_available_buses.php',
                        method: 'GET',
                        data: {
                            route: 'Going South' //paramter
                        },
                        success: function(data) {
                            busDropdown.empty();
                            busDropdown.append(
                                $('<option>', {
                                    value: '',
                                    text: 'Choose a bus',
                                    disabled: true,
                                    selected: true
                                })
                            );
                            data.forEach(function(bus) {
                                busDropdown.append(
                                    $('<option>', {
                                        value: bus.bus_id,
                                        text: bus.plate_number
                                    })
                                );
                            });

                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error: ' + status, error);
                        }
                    });

                    var busDetailsContainer;
                    var busAirconditioned;
                    var busId;
                    var busPlateNumber;

                    var schoolIdStopContainer = $('<div>', {
                        id: 'schoolIdStopContainer',
                        class: 'form-group',
                        style: 'border: solid 1px #ccc; padding: 8px'
                    });

                    $('#busSelection').change(function() {
                        isStudentVerificationOn = true;

                        var schoolIdStopContainer = $('#schoolIdStopContainer');

                        //clear container
                        schoolIdStopContainer.empty();
                        clearInterval(updateSlotsInterval);


                        busId = $(this).val();

                        $.ajax({
                            url: '../get_scripts/get_bus_combo.php',
                            method: 'GET',
                            data: {
                                busId: busId
                            },
                            success: function(busData) {

                                //clear previous 
                                busDetails.empty();
                                busDetails.show();

                                //create new container for bus details
                                busDetailsContainer = $('<div>', {
                                    style: 'margin-top: 10px; border-top: 1px solid #ccc;'
                                });

                                //then display bus info and append to busDetailsContainer div
                                busDetailsContainer.append($('<h4>', {
                                    text: 'Bus Details',
                                    class: 'text-center'
                                }));
                                busDetailsContainer.append($('<img>', {
                                    src: busData.busPhoto,
                                    alt: 'Bus Photo',
                                    class: 'img-fluid mx-auto d-block small-image'
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    text: 'Plate Number: ' + busData.plate_number,
                                    id: 'busPlateNumber'
                                }));
                                busPlateNumber = busData.plate_number;
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',

                                    text: 'Driver Name: ' + busData.bus_driver_name
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAirconditioned',
                                    text: 'Airconditioned: ' + (busData.air_conditioned ? 'Yes' : 'No')
                                }));

                                busAirconditioned = busData.air_conditioned;

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAvailableSlots',
                                    text: 'Loading...'
                                }));

                                var ticketCountContainer = $('<div>', {
                                    class: 'input-group mb-3',
                                    id: 'ticketCountContainer'

                                });

                                var ticketCountInput = $('<input>', {
                                    type: 'number',
                                    id: 'ticketCountInput',
                                    name: 'ticketCountInput',
                                    placeholder: 'Enter ticket quantity',
                                    min: 1,
                                    class: 'form-control'

                                });

                                var inputAppendDiv = $('<div>', {
                                    class: 'input-group-append'
                                });

                                var startLoopBtn = $('<btn>', {
                                    class: 'btn btn-success',
                                    text: 'Start',
                                    id: 'startLoopBtn'
                                });

                                busAvailableSlots = busData.available_slots;
                                updateSlotsInterval = setInterval(updateAvailableSlots, 5000);

                                ticketCountContainer.append(ticketCountInput);
                                ticketCountContainer.append(inputAppendDiv);
                                inputAppendDiv.append(startLoopBtn);

                                //append bus details to busDetails div
                                busDetails.append(busDetailsContainer);
                                busDetailsContainer.append(ticketCountContainer);
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX error: ' + status, error);

                            }
                        });

                        function updateAvailableSlots() {
                            $.ajax({
                                url: '../../../../rev/rev_get_files/get_available_slots.php',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    busId: busId
                                },
                                success: function(response) {
                                    if (response.hasOwnProperty('availableSlots')) {
                                        $('#busAvailableSlots').text('Available Slots: ' + response.availableSlots);
                                    }
                                },
                                error: function() {
                                    $('#availableSlots').text('N/A');
                                }
                            });
                        }



                    });






                    var schoolIdStopContainer = $('<div>', {
                        id: 'schoolIdStopContainer',
                        class: 'form-group',
                        style: 'border: solid 1px #ccc; padding: 8px'
                    });

                    var ticketForm = $('<form>', {
                        id: 'ticketForm',
                    });


                    var labelAndToggleVerificationBtnContainer = $('<div>', {
                        id: 'labelAndToggleVerificationBtnContainer',
                        class: 'form-group row g-2',
                        style: 'margin-bottom: 2px'
                    });

                    var schoolIdLabelDiv = $('<div>', {
                        class: 'col-sm',

                    });

                    var toggleVerificationDiv = $('<div>', {
                        class: 'col-auto'
                    });

                    var toggleVerificationBtn = $('<button>', {
                        id: 'toggleStudentVerification',
                        class: 'btn btn-success btn-sm',
                        text: 'Verification On',
                        type: 'button'
                    });

                    var splitFareDiv = $('<div>', {
                        id: 'splitFareDiv',
                        class: 'input-group',
                        style: 'margin-top: 5px; margin-bottom: 5px;'
                    });

                    var confirmAndCancelBtnDiv = $('<div>', {
                        class: 'justify-content-center d-flex my-2'

                    });

                    var confirmBtn = $('<button>', {
                        class: 'btn btn-primary',
                        text: 'Confirm',
                        type: 'button'
                    });

                    var cancelBtn = $('<button>', {
                        id: 'cancelButton',
                        class: 'btn btn-outline-danger',
                        text: 'Cancel',
                        type: 'button'
                    });

                    var fareDictionary = {
                        'Compostela': 10.00,
                        'Liloan': 20.00,
                        'Consolacion': 30.00,
                        'Mandaue': 40.00,
                        'Cebu': 50.00
                    };

                    //modify e fare dictionary if bus is tugnaw
                    /*if (busIsAirConditioned) {
                        fareDictionary = {
                            'Danao': 15.00,
                            'Carmen': 25.00,
                            'Catmon': 35.00,
                            'Sogod': 45.00
                        };
                    }*/

                    var ticketData = [];
                    var currentTicketIndex = 0;

                    $(document).on('click', '#startLoopBtn', function() {

                        var emptySchoolIdStopContainer = $('#schoolIdStopContainer');
                        var available_seats_error = $('#available_seats_error');

                        //show ticket form
                        var ticketCount = $('#ticketCountInput').val();
                        ticketCount = ticketCount.replace(/^0+/, '');
                        ticketCount = ticketCount.split('.')[0];

                        $('#confirmButton').prop('disabled', true);
                        $('#toggleStudentVerification').removeClass('btn-danger').addClass('btn-success').text('Verification On');
                        isStudentVerificationOn = true;
                        currentTicketIndex = 0;
                        ticketData = [];

                        confirmBtn.empty();
                        schoolIdLabelDiv.empty();
                        splitFareDiv.empty();
                        ticketForm.empty();



                        if (ticketCount <= busAvailableSlots && ticketCount > 0) {

                            if (currentTicketIndex < ticketCount) {

                                //show form if valid ticket count
                                ticketCountContainer = $('#ticketCountContainer');
                                ticketCountContainer.hide();
                                available_seats_error.hide();
                                emptySchoolIdStopContainer.empty();

                                ticketCountContainer.append(schoolIdStopContainer);
                                schoolIdStopContainer.show();

                                schoolIdLabelDiv.append($('<label>', {
                                    for: 'schoolIdInput',
                                    text: 'School Id:'
                                }))

                                labelAndToggleVerificationBtnContainer.append(schoolIdLabelDiv)
                                toggleVerificationDiv.append(toggleVerificationBtn);
                                labelAndToggleVerificationBtnContainer.append(toggleVerificationDiv)



                                schoolIdStopContainer.append(ticketForm);


                                ticketForm.append(labelAndToggleVerificationBtnContainer);

                                var schoolIdInput = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'schoolIdInput',
                                    name: 'schoolIdInput',
                                    placeholder: 'Enter school ID',
                                    required: true
                                });

                                var studentExistingBookingStatus = $('<input>', {
                                    type: 'text',
                                    id: 'studentExistingBookingStatus',
                                    name: 'studentExistingBookingStatus',
                                    hidden: true
                                })

                                var hasPassedCooldownStatus = $('<input>', {
                                    type: 'text',
                                    hidden: true,
                                    id: 'hasPassedCooldownStatus',
                                    name: 'hasPassedCooldownStatus'
                                })

                                ticketForm.append(schoolIdInput);
                                ticketForm.append($('<p>', {
                                    id: 'hasNotPassedCooldownPeriodErrorMessage',
                                    text: '',
                                    style: 'color: red;'
                                }))
                                ticketForm.append(studentExistingBookingStatus);
                                ticketForm.append(hasPassedCooldownStatus);

                                ticketForm.append($('<label>', {
                                    for: 'stopSelection',
                                    text: 'Stop:'
                                }));

                                var stopSelection = $('<select>', {
                                    id: 'stopSelection',
                                    name: 'stopSelection',
                                    class: 'form-control',
                                    required: true
                                })

                                stopSelection.append($('<option>', {
                                    value: '',
                                    text: 'Choose Stop',
                                    selected: true,
                                    disabled: true
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Compostela',
                                    text: 'Compostela'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Liloan',
                                    text: 'Liloan'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Consolacion',
                                    text: 'Consolacion'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Mandaue',
                                    text: 'Mandaue'
                                }));

                                stopSelection.append($('<option>', {
                                    value: 'Cebu',
                                    text: 'Cebu'
                                }));
                                ticketForm.append(stopSelection);

                                splitFareDiv.append($('<span>', {
                                    class: 'input-group-text',
                                    text: 'Fare:'

                                }));

                                var fare = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'fare',
                                    name: 'fare',
                                    readonly: true
                                });

                                splitFareDiv.append(fare);


                                /*splitFareDiv.append($('<span>', {
                                    class: 'input-group-text',
                                    text: 'Total Fare:'
                                }));*/
                                var totalFare = $('<input>', {
                                    type: 'text',
                                    class: 'form-control',
                                    id: 'totalFare',
                                    name: 'totalFare',
                                    readonly: true,
                                    disabled: true
                                });



                                ticketForm.append(splitFareDiv);



                                confirmBtn = $('<button>', {
                                    class: 'btn btn-primary',
                                    text: 'Confirm',
                                    type: 'button',
                                    id: 'confirmButton',
                                    disabled: true
                                });

                                confirmBtn.append($('<span>', {
                                    class: 'badge text-bg-secondary',
                                    text: ticketCount,
                                    id: 'ticketCountBadge'
                                }))

                                confirmAndCancelBtnDiv.append(confirmBtn, cancelBtn);
                                ticketForm.append(confirmAndCancelBtnDiv);

                                busDetailsContainer.append(schoolIdStopContainer);



                                if (isStudentVerificationOn) {
                                    $('#schoolIdInput').on('keyup', function() {
                                        var schoolId = $('#schoolIdInput').val();
                                        //check shoolid on keyup
                                        $.ajax({
                                            url: '../get_scripts/verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = response;
                                                if (responseData.schoolIdExists) {

                                                    //if school id exissts
                                                    if (responseData.lastRequestStatus) {

                                                        if (responseData.passedCooldownPeriod) {
                                                            //good to go
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                            $('#studentExistingBookingStatus').val('true');
                                                            $('#hasPassedCooldownStatus').val('true');
                                                            $('#confirmButton').prop('disabled', false);
                                                        } else {
                                                            //if student has not yet passed last request cooldown status
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                            // disable confirm button
                                                            $('#confirmButton').prop('disabled', true);
                                                        }

                                                    } else {
                                                        //theres no last reqesut, set to false
                                                        $('#studentExistingBookingStatus').val('false');
                                                        //enable button cuz this must be student first tiem request
                                                        $('#confirmButton').prop('disabled', false);
                                                    }
                                                } else {
                                                    //school id not exist
                                                    $('#studentExistingBookingStatus').val('');
                                                    //so disable
                                                    $('#confirmButton').prop('disabled', true);
                                                }
                                            },
                                            error: function() {}
                                        });

                                    });
                                }

                                $('#toggleStudentVerification').on('click', function() {
                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                    var schoolIdLabel = $('label[for="schoolIdInput"]');
                                    var schoolIdInput = $('#schoolIdInput');

                                    if (isStudentVerificationOn) {
                                        //dont give a shit mode
                                        schoolIdLabel.text('Passenger Name:');
                                        schoolIdInput.attr('placeholder', 'Enter passenger name');
                                        isStudentVerificationOn = false;

                                        $('#schoolIdInput').off('keyup');

                                        $('#confirmButton').prop('disabled', false);

                                        $(this).removeClass('btn-success').addClass('btn-danger').text('Verification Off');
                                    } else {
                                        //school Id mode
                                        schoolIdLabel.text('School Id:');
                                        schoolIdInput.attr('placeholder', 'Enter school ID');
                                        isStudentVerificationOn = true;
                                        $('#confirmButton').prop('disabled', true);

                                        var schoolId = $('#schoolIdInput').val();
                                        $.ajax({
                                            url: '../get_scripts/verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = response;
                                                if (responseData.schoolIdExists) {

                                                    // School ID exists
                                                    if (responseData.lastRequestStatus) {

                                                        if (responseData.passedCooldownPeriod) {
                                                            //good to go
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                            $('#studentExistingBookingStatus').val('true');
                                                            $('#hasPassedCooldownStatus').val('true');
                                                            $('#confirmButton').prop('disabled', false);
                                                        } else {
                                                            //if student has not yet passed last request cooldown status
                                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                            // disable confirm button
                                                            $('#confirmButton').prop('disabled', true);
                                                        }

                                                    } else {
                                                        $('#studentExistingBookingStatus').val('false');
                                                        $('#confirmButton').prop('disabled', false);
                                                    }
                                                } else {
                                                    $('#studentExistingBookingStatus').val('');
                                                    $('#confirmButton').prop('disabled', true);
                                                }
                                            },
                                            error: function() {

                                            }
                                        });

                                        //set up keyup event handler again when verification is turned on
                                        $('#schoolIdInput').on('keyup', function() {
                                            var schoolId = $('#schoolIdInput').val();
                                            $.ajax({
                                                url: '../get_scripts/verify_schoolId.php',
                                                type: 'POST',
                                                data: {
                                                    schoolId: schoolId
                                                },
                                                success: function(response) {
                                                    var responseData = response;
                                                    if (responseData.schoolIdExists) {

                                                        if (responseData.lastRequestStatus) {

                                                            if (responseData.passedCooldownPeriod) {
                                                                //good to go
                                                                $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                                $('#studentExistingBookingStatus').val('true');
                                                                $('#hasPassedCooldownStatus').val('true');
                                                                $('#confirmButton').prop('disabled', false);
                                                            } else {
                                                                //if student has not yet passed last request cooldown status
                                                                $('#hasNotPassedCooldownPeriodErrorMessage').text('this schoolId already has made a request for tha past 2 hours, please check bookings table');
                                                                // disable confirm button
                                                                $('#confirmButton').prop('disabled', true);
                                                            }

                                                        } else {
                                                            $('#studentExistingBookingStatus').val('false');
                                                            $('#confirmButton').prop('disabled', false);
                                                        }
                                                    } else {
                                                        $('#studentExistingBookingStatus').val('');
                                                        $('#confirmButton').prop('disabled', true);
                                                    }
                                                },
                                                error: function() {}
                                            });
                                        });

                                        $(this).removeClass('btn-danger').addClass('btn-success').text('Verification On');
                                    }


                                });


                                $('#stopSelection').on('change', function() {
                                    var selectedStop = $(this).val();
                                    var fareInput = $('#fare');

                                    console.log(busAirconditioned);
                                    //check fare input if fare input in fare dicktionary
                                    var baseFare = fareDictionary[selectedStop] || 0.00; // Default to 0.00 if fare not found

                                    //if air conditioned add 10
                                    var finalFare = busAirconditioned ? baseFare + 10.00 : baseFare;

                                    //update fare input
                                    fareInput.val(finalFare.toFixed(2));
                                });

                                $('#cancelButton').on('click', function() {

                                    currentTicketIndex = 0;
                                    ticketData = [];
                                    schoolIdStopContainer.hide();
                                    $('#ticketCountInput').val('');
                                    confirmAndCancelBtnDiv.empty();
                                    ticketCountContainer.show();

                                })


                                //confirm button clicked
                                $('#confirmButton').on('click', function() {
                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');

                                    var schoolIdInput = $('#schoolIdInput');
                                    var schoolId = $('#schoolIdInput').val();
                                    var stop = $('#stopSelection').val();
                                    var fare = $('#fare').val();
                                    var lastBookingStatus = $('#studentExistingBookingStatus').val();
                                    console.log(lastBookingStatus);
                                    var ticketCount = $('#ticketCountInput').val();
                                    const ticketCode = generateUniqueTicketCode();

                                    if (schoolId.trim() !== '' && stop != null) {
                                        //dpt di magbalik2 ang id or name gi enter
                                        if (!isStudentIdRepeated(schoolId)) {
                                            //collect data, store in array first
                                            ticketData.push({
                                                ticketCode: ticketCode,
                                                busPlateNumber: busPlateNumber,
                                                schoolId: schoolId,
                                                stop: stop,
                                                fare: fare,
                                                isStudentVerified: isStudentVerificationOn,
                                                lastBookingStatus: lastBookingStatus
                                            });

                                            //increment
                                            currentTicketIndex++;

                                            //clear, get ready for next iteration
                                            schoolIdInput.val('');
                                            $('#stopSelection').val('');
                                            $('#fare').val('');

                                            if (currentTicketIndex >= ticketCount) {
                                                if (ticketData.length > 0) {

                                                    var summaryContent = "";

                                                    //populate summary tciekt
                                                    for (var i = 0; i < ticketData.length; i++) {
                                                        var ticket = ticketData[i];
                                                        summaryContent += '<p><strong>Ticket Code:</strong> ' + ticket.ticketCode + '</p>';
                                                        summaryContent += '<p><strong>Bus Plate number:</strong> ' + ticket.busPlateNumber + '</p>';
                                                        summaryContent += '<p><strong>School ID / Name:</strong> ' + ticket.schoolId + '</p>';
                                                        summaryContent += '<p><strong>Stop:</strong> ' + ticket.stop + '</p>';
                                                        summaryContent += '<p><strong>Fare:</strong> ' + ticket.fare + '</p>';
                                                        summaryContent += '<hr>';
                                                    }

                                                    //set summary content to summary bodeh
                                                    $('#summaryBody').html(summaryContent);

                                                    //show modal
                                                    $('#ticketModal').modal('hide');
                                                    $('#summaryModal').modal('show');
                                                } else {
                                                    console.log('No ticket data to display.');
                                                }


                                            } else {
                                                $('#ticketCountBadge').text(ticketCount - currentTicketIndex);
                                            }
                                        } else {
                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                            $('#hasNotPassedCooldownPeriodErrorMessage').text('Student ID already entered.');
                                        }
                                    } else {
                                        $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                        $('#hasNotPassedCooldownPeriodErrorMessage').text('School ID or Stop cannot be empty.');
                                    }
                                });

                                //check if the student ID is repeated
                                function isStudentIdRepeated(newStudentId) {
                                    return ticketData.some(ticket => ticket.schoolId === newStudentId);
                                }

                                function generateUniqueTicketCode() {
                                    const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                    const codeLength = 8;

                                    let ticketCode;
                                    let codeExistsInTickets;

                                    do {
                                        ticketCode = '';
                                        for (let i = 0; i < codeLength; i++) {
                                            ticketCode += characters.charAt(Math.floor(Math.random() * characters.length));
                                        }

                                        codeExistsInTickets = checkCodeInTickets(ticketCode);
                                    } while (codeExistsInTickets);

                                    return ticketCode;
                                }

                                function checkCodeInTickets(code) {
                                    //check if ticketcode in my array
                                    return ticketData.some(ticket => ticket.ticketCode === code);
                                }


                                $('#confirmPrintButton').on('click', function() {
                                    if (ticketData.length > 0) {
                                        $.ajax({
                                            url: 'admin_process_tickets.php',
                                            type: 'POST',
                                            data: {
                                                ticketData: JSON.stringify(ticketData)
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                var tickets = response.ticketData;
                                                console.log(response);

                                                if (tickets && tickets.length > 0) {
                                                    var printWindow = window.open('', '_blank');

                                                    var allTicketsHtml = tickets.map(function(ticket) {
                                                        return getTicketHtml(ticket, getFormattedDateTime());
                                                    }).join('');

                                                    printWindow.document.write(allTicketsHtml);
                                                    printWindow.document.close();
                                                    printWindow.print();
                                                    printWindow.close();

                                                    currentTicketIndex = 0;
                                                    ticketData = [];
                                                    schoolIdStopContainer.hide();
                                                    ticketCountContainer.show();
                                                    $('#ticketCountInput').val('');
                                                    $('#studentExistingBookingStatus').val('');
                                                    $('#hasNotPassedCooldownPeriodErrorMessage').text('');
                                                    $('#summaryModal').modal('hide');
                                                    location.reload();
                                                } else {
                                                    console.error('No ticket data received');
                                                }
                                            },
                                            error: function(error) {
                                                console.error(error);
                                            }
                                        });
                                    } else {
                                        console.log('No ticket data to confirm and print.');
                                        $('#summaryModal').modal('hide');
                                    }
                                });

                                function getTicketHtml(ticket, todaysDate) {
                                    var ticketHtml = `
                                    <div class="ticket-container">
                                        <p><strong>---------------- CTU-Danao [ Bus Ride Ticket ] ----------------</strong></p>
                                        <p><strong>Bus: [${ticket.busPlateNumber}] </strong>| <strong>Ticket# [${ticket.ticketCode}]</strong></p>
                                        <p><strong>Student ID:</strong> ${ticket.schoolId} | <strong>Stop:</strong> ${ticket.stop} | Php${ticket.fare}</p>
                                        <p><strong>Receipt#:</strong> ${ticket.transactionCode} | <strong>Date:</strong> ${todaysDate}</p>
                                        <p><strong>Cashier: </strong> <?php echo $adminName ?></p>
                                        <p><strong>Thanks for riding with us! :D</strong></p>
                                        <p>------------------------------- cashier ---------------------------------</p>
                                    </div>
    `;
                                    return ticketHtml;
                                }


                                function getFormattedDateTime() {
                                    var today = new Date();
                                    var dd = String(today.getDate()).padStart(2, '0');
                                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                                    var yyyy = today.getFullYear();
                                    var hh = today.getHours();
                                    var min = String(today.getMinutes()).padStart(2, '0');
                                    var sec = String(today.getSeconds()).padStart(2, '0');

                                    var ampm = hh >= 12 ? 'PM' : 'AM';
                                    hh = hh % 12;
                                    hh = hh ? hh : 12;

                                    return mm + '/' + dd + '/' + yyyy + ' ' + hh + ':' + min + ':' + sec + ' ' + ampm;
                                }
                            }

                        } else {

                            if (ticketCount <= 0) {
                                available_seats_error.hide();
                                emptySchoolIdStopContainer.empty();
                            } else {
                                emptySchoolIdStopContainer.empty();

                                available_seats_error.show();

                                busDetailsContainer.append($('<p>', {
                                    id: 'available_seats_error',
                                    color: 'red',
                                }).css('color', 'red'));

                                var slot_error = $('#available_seats_error');

                                slot_error.text("That's more than the available slots. Are you trying to take the bus driver's seat too?");
                            }



                        }

                    });

                }


            })



            $('#okButton').on('click', function() {
                location.reload();
            });
        });
    </script>



</body>

</html>