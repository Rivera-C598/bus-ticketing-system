<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../admin_login.php");
    exit();
}
include '../../../../database_config/db_config.php';

$sql = "SELECT bus_id, busPhoto, plate_number, bus_driver_name,driver_contact_num, route, capacity, air_conditioned, created_at, updated_at, status, confirmed_tickets FROM buses;";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />

    <title>Manage Buses - Bus Ticketing System</title>
    <style>
        .table tbody tr {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <div class="container text-center py-5">
            <h2>Manage Buses</h2>
            <a href="../mirage/admin_control_panel.php" class="btn btn-outline-primary btn-md">Control Panel</a>
            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addBusModal">Add a new bus</button>
        </div>


        <div class="container mt-5">
            <div class="row">

                <div class="input-group py-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <button class="btn btn-primary" id="searchButton" type="button">Search</button>
                    <button class="btn btn-success" id="clearButton" type="button">Refresh</button>

                </div>

                <div class="col-lg-12">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Bus Photo</th>
                                <th scope="col">Plate Number</th>
                                <th scope="col">Driver Name </th>
                                <th scope="col">Driver Contact Number</th>
                                <th scope="col">Route</th>
                                <th scope="col">Capacity</th>
                                <th scope="col">Tickets Sold</th>
                                <th scope="col">Air Conditioned</th>
                                <th scope="col">Added On</th>
                                <th scope="col">Updated On</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row) : ?>
                                <tr data-plate-number="<?= $row['plate_number'] ?>">
                                    <td class="busId"><?= $row['bus_id'] ?></td>
                                    <td class="busPhoto"><img src='<?= $row['busPhoto'] ?>' alt='Bus Photo' width='80' height='auto'></td>
                                    <td class="plate_number"><?= $row['plate_number'] ?></td>
                                    <td class="bus_driver_name"><?= $row['bus_driver_name'] ?></td>
                                    <td class="driver_contact_num"><?= $row['driver_contact_num'] ?></td>
                                    <td class="route"><?= $row['route'] ?></td>
                                    <td class="capacity"><?= $row['capacity'] ?></td>
                                    <td class="confirmed_tickets"><?= $row['confirmed_tickets'] ?></td>
                                    <td class="air_conditioned"><?= ($row['air_conditioned'] ? "Yes" : "No") ?></td>
                                    <td class="created_at"><?= $row['created_at'] ?></td>
                                    <td class="updated_at"><?= $row['updated_at'] ?></td>
                                    <td class="status"><?= $row['status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                </div>

            </div>
        </div>


    </div>

    <div class="modal fade" id="busDetailsModal" tabindex="-1" aria-labelledby="busDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="busDetailsModalLabel">Bus Details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img id="modalBusPhoto" src="" alt="Bus Photo" style="max-width: 200px; max-height: 150px;">
                    </div>
                    <div>
                        <p><strong>Id: </strong><span id="modalBusId"></span></p>
                        <p><strong>Bus Plate Number: </strong><span id="modalPlateNumber"></span></p>
                        <p><strong>Bus Driver Name: </strong><span id="modalDriverName"></span></p>
                        <p><strong>Driver Contact Number: </strong><span id="modalDriverContactNum"></span></p>
                        <p><strong>Route: </strong><span id="modalRoute"></span></p>
                        <p><strong>Capacity: </strong><span id="modalCapacity"></span></p>
                        <p><strong>Tickets Sold: </strong><span id="modalTicketsSold"></span></p>
                        <p><strong>Air Conditioned: </strong><span id="modalAirConditioned"></span></p>
                        <p><strong>Status: </strong><span id="modalStatus"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Toggle Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item toggle-status available">available</a></li>
                            <li><a class="dropdown-item toggle-status unavailable">unavailable</a></li>
                            <li><a class="dropdown-item toggle-status full">full</a></li>
                        </ul>
                    </div>
                    <a id="editBusButton" class="btn btn-success">Edit Bus</a>
                    <a id="deleteBusButton" class="btn btn-danger">Delete Bus</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmStatusChangeModal" tabindex="-1" aria-labelledby="confirmStatusChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmStatusChangeModalLabel">Confirm Status Change</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <p><strong>Bus Plate number: </strong><span id="modalBusPlateNum"></span></p>
                        <p><strong>from: </strong><span id="modalOldStatus"></span></p>
                        <p><strong>to: </strong><span id="modalNewStatus"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary">Confirm</a>
                    <a class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeletionModal" tabindex="-1" aria-labelledby="confirmDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeletionModalLabel">Confirm Bus Deletion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <p class="text-center"><strong>Confirm Bus deletion?</p>

                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" id="confirmBusDeletionBtn">Confirm</a>
                    <a class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBusModal" tabindex="-1" aria-labelledby="editBusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBusModalLabel">Edit Bus</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">

                        <img id="editBusPhoto" src="" alt="Bus Photo" style="max-width: 150px; height: 100px; display: block; margin: 0 auto;">

                        <form id="editBusForm" enctype="multipart/form-data">
                            <input type="hidden" id="busId" name="busId">
                            <input type="hidden" id="currentBusPhoto" name="currentBusPhoto">
                            <div class="mb-3">
                                <label for="newBusPhoto" class="form-label">Change Bus Photo</label>
                                <input type="file" class="form-control" id="newBusPhoto" name="newBusPhoto">
                            </div>
                            <div class="mb-3">
                                <label for="editPlateNumber" class="form-label">Bus Plate Number</label>
                                <input type="text" class="form-control" id="editPlateNumber" name="editPlateNumber">
                            </div>
                            <div class="mb-3">
                                <label for="editDriverName" class="form-label">Bus Driver Name</label>
                                <input type="text" class="form-control" id="editDriverName" name="editDriverName">
                            </div>
                            <div class="mb-3">
                                <label for="editDriverContactNum" class="form-label">Driver Contact Number</label>
                                <input type="text" class="form-control" id="editDriverContactNum" name="editDriverContactNum">
                            </div>
                            <div class="mb-3">
                                <label for="editRoute" class="form-label">Route</label>
                                <select class="form-select" id="editRoute" name="editRoute">
                                    <option value="Going North">Going North</option>
                                    <option value="Going South">Going South</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editCapacity" class="form-label">Capacity:</label>
                                <input type="number" class="form-control" id="editCapacity" name="editCapacity">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="editAirConditioned" name="editAirConditioned">
                                <label class="form-check-label" for="editAirConditioned">Air Conditioned</label>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="updateBusButton">Update Bus</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table tbody tr').on('click', function() {
                var busId = $(this).find('.busId').text();
                var busPhoto = $(this).find('.busPhoto img').attr('src');
                var busPlateNumber = $(this).find('.plate_number').text();
                var busDriverName = $(this).find('.bus_driver_name').text();
                var driverContactNum = $(this).find('.driver_contact_num').text();
                var route = $(this).find('.route').text();
                var capacity = $(this).find('.capacity').text();
                var ticketsSold = $(this).find('.confirmed_tickets').text();
                var airConditioned = $(this).find('.air_conditioned').text();
                var created_at = $(this).find('.created_at').text();
                var updated_at = $(this).find('.updated_at').text();
                var status = $(this).find('.status').text();

                $('#modalBusPhoto').attr('src', busPhoto);
                $('#modalBusId').text(busId);
                $('#modalPlateNumber').text(busPlateNumber);
                $('#modalDriverName').text(busDriverName);
                $('#modalDriverContactNum').text(driverContactNum);
                $('#modalRoute').text(route);
                $('#modalCapacity').text(capacity);
                $('#modalTicketsSold').text(ticketsSold);
                $('#modalAirConditioned').text(airConditioned);
                $('#modalCreatedAt').text(created_at);
                $('#modalUpdatedAt').text(updated_at);
                $('#modalStatus').text(status);

                var hideEditBusBtn = $('#editBusButton');
                
                if(status == 'unavailable'){
                    hideEditBusBtn.show();
                }else if(status == 'available' || status == 'full'){
                    hideEditBusBtn.hide();
                }

                $('#busDetailsModal').modal('show');

                var modalStatus = $('#modalStatus').text();
                console.log(modalStatus);

                if (modalStatus === 'full') {
                    $('.toggle-status').hide(); 
                    $('.toggle-status.unavailable, .toggle-status.available').show();
                }

                if (modalStatus === 'unavailable') {
                    $('.toggle-status').hide();
                    $('.toggle-status.available').show();
                }

                if (modalStatus === 'available') {
                    $('.toggle-status').hide();
                    $('.toggle-status.unavailable, .toggle-status.full').show();
                }

                $('.toggle-status').on('click', function() {
                    var newStatus = $(this).text();
                    var currentStatus = $('#modalStatus').text();
                    var plateNum = $('#modalPlateNumber').text();

                    if (newStatus !== currentStatus) {
                        $('#busDetailsModal').modal('hide');
                        $('#confirmStatusChangeModal').modal('show');
                        $('#modalBusPlateNum').text(plateNum);
                        $('#modalOldStatus').text(currentStatus);
                        $('#modalNewStatus').text(newStatus);
                    }
                });
            });

            $('#confirmStatusChangeModal .btn-primary').on('click', function() {

                var busPlateNum = $('#modalBusPlateNum').text();
                var oldStatus = $('#modalOldStatus').text();
                var newStatus = $('#modalNewStatus').text();

                var statusChangeData = {
                    busPlateNum: busPlateNum,
                    oldStatus: oldStatus,
                    newStatus: newStatus
                };

                $.ajax({
                    type: 'POST',
                    url: 'mbs/toggle_bus_status.php',
                    data: statusChangeData,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert('Failed to update bus details.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });

                $('#confirmStatusChangeModal').modal('hide');
            });

            $('#editBusButton').on('click', function() {
                
                    $('#busId').val($('#modalBusId').text());
                    $('#editPlateNumber').val($('#modalPlateNumber').text());
                    $('#editDriverName').val($('#modalDriverName').text());
                    $('#editDriverContactNum').val($('#modalDriverContactNum').text());
                    $('#editRoute').val($('#modalRoute').text());
                    $('#editCapacity').val($('#modalCapacity').text());
                    $('#editAirConditioned').prop('checked', ($('#modalAirConditioned').text() === 'Yes'));
                    $('#editStatus').text($('#modalStatus').text());

                    var busPhotoSrc = $('#modalBusPhoto').attr('src');
                    $('#editBusPhoto').attr('src', busPhotoSrc);
                    $('#currentBusPhoto').val(busPhotoSrc);


                    $('#busDetailsModal').modal('hide');
                    $('#editBusModal').modal('show');
                
            });

            $('#updateBusButton').on('click', function() {
                $('#editBusForm').submit();
            });


            $('#editBusForm').submit(function(e) {
                e.preventDefault(); 

                var formData = new FormData(this);
                console.log(formData)

                $.ajax({
                    type: 'POST',
                    url: 'update_bus.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            $('#editBusModal').modal('hide');
                            window.location.reload();
                        } else {
                            alert('Failed to update bus details.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });
            });

            $(document).on('click', '#deleteBusButton', function() {

                console.log('im clicked');
                $('#busDetailsModal').modal('hide');
                $('#confirmDeletionModal').modal('show');


            });

            $(document).on('click', '#confirmBusDeletionBtn', function() {
                var busId = $('#modalBusId').text();

                var deleteBusData = {
                    busId: busId
                };

                $.ajax({
                    type: 'POST',
                    url: 'delete_bus.php',
                    data: deleteBusData,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert('Failed to delete bus.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });

                $('#confirmBusDeletionModal').modal('hide');
                $('#busDetailsModal').modal('hide');

            });


            //search function
            $('#searchButton').on('click', function() {
                var searchValue = $('#searchInput').val().toLowerCase(); //get search input and convert to lowercase

                //loop thru each table
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase(); //get what we find and convert to lowercase

                    if (rowText.includes(searchValue)) {
                        $(this).show(); //show the row if something was found
                    } else {
                        $(this).hide(); //hide the row if nothing was found
                    }
                });
            });

            //clear button function
            $('#clearButton').on('click', function() {
                $('#searchInput').val('');
                $('table tbody tr').show();
            });


        });
    </script>


    <!-- add Bus Modal -->
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

                    <form action="add_bus.php" method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="plateNumber" class="form-label">Plate number:</label>
                            <input type="text" class="form-control" id="plateNumber" name="plateNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="driverName" class="form-label">Bus driver name:</label>
                            <input type="text" class="form-control" id="driverName" name="driverName" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactNum" class="form-label">driver contact number:</label>
                            <input type="text" class="form-control" id="contactNum" name="contactNum" placeholder="+63----------" required>
                        </div>
                        <div class="mb-3">
                            <label for="busPhoto" class="form-label">Bus Photo (max 5MB) (Optional):</label>
                            <input type="file" id="busPhoto" name="busPhoto" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="route" class="form-label">Route:</label>
                            <select class="form-control" id="route" name="route" required>
                                <option value="Going North">Going North</option>
                                <option value="Going South">Going South</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity:</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="mb-3 form-check">
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

    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; 2023 Bus Ticketing Service</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>