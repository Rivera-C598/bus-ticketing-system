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

        .badge.text-bg-secondary {
            background-color: orange;
            color: black;
            /* Set text color to white for contrast */
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
                            <a class="btn btn-success btn-lg btn-block" data-bs-toggle="modal" data-bs-target="#ticketModal">
                                + Tickets
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="transactions.php" class="btn btn-primary btn-lg btn-block">Transaction history</a>
                            <a href="students.php" class="btn btn-primary btn-lg btn-block">Student reference</a>
                            <a href="admin_logout.php" class="btn btn-danger btn-lg btn-block">Log out</a>
                        </div>
                    </div>

                    <!-- tobol container -->
                    <div class="container mt-5">
                        <div class="row">
                            <h2 class="text-center">Bookings</h2>

                            <!-- search bar -->
                            <div class="input-group py-3">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                            </div>

                            <div class="col-lg-12">
                                <!-- table start -->
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generateTicketsBtn">Generate</button>
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
                    url: 'update_table.php',
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


    <script>
        $(document).ready(function() {
            var routeDropdown = $('#route');

            var form = $('#dynamicForm');
            form.empty();

            $('#generateTicketsBtn').hide();

            var busAvailableSlots;
            var isStudentVerificationOn = true;

            //route selection first
            $('#route').change(function() {
                routeSelection = $(this).val();
                form.empty();

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

                    //ajax 
                    $.ajax({
                        url: 'get_available_buses.php',
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

                    $('#busSelection').change(function() {

                        var schoolIdStopContainer = $('#schoolIdStopContainer');

                        //clear container
                        schoolIdStopContainer.empty();

                        var busDetails = $('#busDetails');
                        var busId = $(this).val();


                        $.ajax({
                            url: 'get_bus_combo.php',
                            method: 'GET',
                            data: {
                                busId: busId
                            },
                            success: function(busData) {

                                //clear previous shit
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
                                    class: 'img-fluid mx-auto d-block',
                                    style: 'width: 80px; height: 80px;'
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    text: 'Plate Number: ' + busData.plate_number
                                }));
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',

                                    text: 'Driver Name: ' + busData.bus_driver_name
                                }));
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAvailableSlots',
                                    text: 'Available Slots: ' + busData.available_slots
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

                    // Modify the fare dictionary if the bus is air-conditioned
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

                        //show ticket form
                        var ticketCount = $('#ticketCountInput').val();
                        ticketCount = ticketCount.replace(/^0+/, '');
                        ticketCount = ticketCount.split('.')[0];

                        var emptySchoolIdStopContainer = $('#schoolIdStopContainer');
                        var available_seats_error = $('#available_seats_error');

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
                                $('#schoolIdStopContainer').show();

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
                                    placeholder: 'Enter school ID'
                                });

                                ticketForm.append(schoolIdInput);

                                ticketForm.append($('<label>', {
                                    for: 'stopSelection',
                                    text: 'Stop:'
                                }));

                                var stopSelection = $('<select>', {
                                    id: 'stopSelection',
                                    name: 'stopSelection',
                                    class: 'form-control'
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

                                ticketForm.append(confirmBtn);
                                ticketForm.append(cancelBtn);

                                busDetailsContainer.append(schoolIdStopContainer);



                                if (isStudentVerificationOn) {
                                    $('#schoolIdInput').on('keyup', function() {
                                        var schoolId = $('#schoolIdInput').val();
                                        //check shoolid on keyup
                                        $.ajax({
                                            url: 'verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = JSON.parse(response);
                                                if (responseData.exists) {
                                                    $('#confirmButton').prop('disabled', false);

                                                } else {
                                                    $('#confirmButton').prop('disabled', true);

                                                }
                                            },
                                            error: function() {}
                                        });

                                    });
                                }

                                $('#toggleStudentVerification').on('click', function() {
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
                                            url: 'verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = JSON.parse(response);
                                                if (responseData.exists) {
                                                    $('#confirmButton').prop('disabled', false);
                                                } else {
                                                    console.log(isStudentVerificationOn);
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
                                                url: 'verify_schoolId.php',
                                                type: 'POST',
                                                data: {
                                                    schoolId: schoolId
                                                },
                                                success: function(response) {
                                                    var responseData = JSON.parse(response);
                                                    if (responseData.exists) {
                                                        $('#confirmButton').prop('disabled', false);
                                                    } else {
                                                        console.log(isStudentVerificationOn);
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

                                    //lookup the fare from fare dictionary based on the selected stop
                                    var fare = fareDictionary[selectedStop];

                                    //update with correspoding fare
                                    fareInput.val(fare);
                                });

                                $('#cancelButton').on('click', function() {

                                    currentTicketIndex = 0;
                                    ticketData = [];
                                    schoolIdStopContainer.hide();
                                    $('#ticketCountInput').val('');
                                    ticketCountContainer.show();

                                })


                                //confirm button clicked
                                $('#confirmButton').on('click', function() {
                                    var schoolIdInput = $('#schoolIdInput');
                                    var schoolId = $('#schoolIdInput').val();
                                    var stop = $('#stopSelection').val();
                                    var fare = $('#fare').val();
                                    var ticketCount = $('#ticketCountInput').val();
                                    console.log(stop);

                                    if (schoolId.trim() !== '' && stop != null) {
                                        //collect data, store in array first
                                        ticketData.push({
                                            schoolId: schoolId,
                                            stop: stop,
                                            fare: fare,
                                            verifyStudent: isStudentVerificationOn
                                        });

                                        //increment currentTicketindex
                                        currentTicketIndex++;

                                        //check if accurate data
                                        console.log(ticketData);

                                        //clear schoolIdInput field and stopSelection
                                        schoolIdInput.val('');
                                        $('#stopSelection').val('');
                                        $('#fare').val('');

                                        if (currentTicketIndex >= ticketCount) {

                                            currentTicketIndex = 0;
                                            ticketData = [];
                                            schoolIdStopContainer.hide();
                                            ticketCountContainer.show();
                                            currentTicketIndex = 0;
                                            $('#ticketCountInput').val('');


                                            //when all is don, we do somthing
                                            //TODO: show summary form and generate ticket button.

                                            console.log('done');

                                        } else {
                                            $('#ticketCountBadge').text(ticketCount - currentTicketIndex);
                                        }
                                    } else {
                                        console.log('School ID and Stop cannot be empty.');
                                    }


                                });

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

                    //ajax 
                    $.ajax({
                        url: 'get_available_buses.php',
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

                    $('#busSelection').change(function() {

                        var schoolIdStopContainer = $('#schoolIdStopContainer');

                        //clear container
                        schoolIdStopContainer.empty();

                        var busDetails = $('#busDetails');
                        var busId = $(this).val();


                        $.ajax({
                            url: 'get_bus_combo.php',
                            method: 'GET',
                            data: {
                                busId: busId
                            },
                            success: function(busData) {

                                //clear previous shit
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
                                    class: 'img-fluid mx-auto d-block',
                                    style: 'width: 80px; height: 80px;'
                                }));

                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    text: 'Plate Number: ' + busData.plate_number
                                }));
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',

                                    text: 'Driver Name: ' + busData.bus_driver_name
                                }));
                                busDetailsContainer.append($('<p>', {
                                    class: 'text-center',
                                    id: 'busAvailableSlots',
                                    text: 'Available Slots: ' + busData.available_slots
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

                    // Modify the fare dictionary if the bus is air-conditioned
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

                        //show ticket form
                        var ticketCount = $('#ticketCountInput').val();
                        ticketCount = ticketCount.replace(/^0+/, '');
                        ticketCount = ticketCount.split('.')[0];

                        var emptySchoolIdStopContainer = $('#schoolIdStopContainer');
                        var available_seats_error = $('#available_seats_error');

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
                                $('#schoolIdStopContainer').show();

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
                                    placeholder: 'Enter school ID'
                                });

                                ticketForm.append(schoolIdInput);

                                ticketForm.append($('<label>', {
                                    for: 'stopSelection',
                                    text: 'Stop:'
                                }));

                                var stopSelection = $('<select>', {
                                    id: 'stopSelection',
                                    name: 'stopSelection',
                                    class: 'form-control'
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

                                ticketForm.append(confirmBtn);
                                ticketForm.append(cancelBtn);

                                busDetailsContainer.append(schoolIdStopContainer);



                                if (isStudentVerificationOn) {
                                    $('#schoolIdInput').on('keyup', function() {
                                        var schoolId = $('#schoolIdInput').val();
                                        //check shoolid on keyup
                                        $.ajax({
                                            url: 'verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = JSON.parse(response);
                                                if (responseData.exists) {
                                                    $('#confirmButton').prop('disabled', false);

                                                } else {
                                                    $('#confirmButton').prop('disabled', true);

                                                }
                                            },
                                            error: function() {}
                                        });

                                    });
                                }

                                $('#toggleStudentVerification').on('click', function() {
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
                                            url: 'verify_schoolId.php',
                                            type: 'POST',
                                            data: {
                                                schoolId: schoolId
                                            },
                                            success: function(response) {
                                                var responseData = JSON.parse(response);
                                                if (responseData.exists) {
                                                    $('#confirmButton').prop('disabled', false);
                                                } else {
                                                    console.log(isStudentVerificationOn);
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
                                                url: 'verify_schoolId.php',
                                                type: 'POST',
                                                data: {
                                                    schoolId: schoolId
                                                },
                                                success: function(response) {
                                                    var responseData = JSON.parse(response);
                                                    if (responseData.exists) {
                                                        $('#confirmButton').prop('disabled', false);
                                                    } else {
                                                        console.log(isStudentVerificationOn);
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

                                    //lookup the fare from fare dictionary based on the selected stop
                                    var fare = fareDictionary[selectedStop];

                                    //update with correspoding fare
                                    fareInput.val(fare);
                                });

                                $('#cancelButton').on('click', function() {

                                    currentTicketIndex = 0;
                                    ticketData = [];
                                    schoolIdStopContainer.hide();
                                    $('#ticketCountInput').val('');
                                    ticketCountContainer.show();

                                })


                                //confirm button clicked
                                $('#confirmButton').on('click', function() {
                                    var schoolIdInput = $('#schoolIdInput');
                                    var schoolId = $('#schoolIdInput').val();
                                    var stop = $('#stopSelection').val();
                                    var fare = $('#fare').val();
                                    var ticketCount = $('#ticketCountInput').val();
                                    console.log(stop);

                                    if (schoolId.trim() !== '' && stop != null) {
                                        //collect data, store in array first
                                        ticketData.push({
                                            schoolId: schoolId,
                                            stop: stop,
                                            fare: fare,
                                            verifyStudent: isStudentVerificationOn
                                        });

                                        //increment currentTicketindex
                                        currentTicketIndex++;

                                        //check if accurate data
                                        console.log(ticketData);

                                        //clear schoolIdInput field and stopSelection
                                        schoolIdInput.val('');
                                        $('#stopSelection').val('');
                                        $('#fare').val('');

                                        if (currentTicketIndex >= ticketCount) {

                                            currentTicketIndex = 0;
                                            ticketData = [];
                                            schoolIdStopContainer.hide();
                                            ticketCountContainer.show();
                                            currentTicketIndex = 0;
                                            $('#ticketCountInput').val('');


                                            //when all is don, we do somthing
                                            //TODO: show summary form and generate ticket button.

                                            console.log('done');

                                        } else {
                                            $('#ticketCountBadge').text(ticketCount - currentTicketIndex);
                                        }
                                    } else {
                                        console.log('School ID and Stop cannot be empty.');
                                    }


                                });

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

        });
    </script>



</body>

</html>