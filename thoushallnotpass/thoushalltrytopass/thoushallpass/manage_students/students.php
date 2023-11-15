<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ../../admin_login.php");
    exit();
}
include '../../../../database_config/db_config.php';

$query = "SELECT id, student_id, name FROM student_reference;";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 15;
$totalItems = count($results);
$totalPages = ceil($totalItems / $itemsPerPage);
$startIndex = ($page - 1) * $itemsPerPage;
$paginatedResults = array_slice($results, $startIndex, $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />

    <title>Student Reference - Bus Ticketing System</title>
    <style>
        .table tbody tr {
            cursor: pointer;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        #title {
            border-bottom: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <div class="container text-center py-3" id="title">
            <h2>Student Reference Table</h2>
            <a href="../mirage/admin_control_panel.php" class="btn btn-outline-primary btn-md">Control Panel</a>
            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
        </div>

        <!-- add student Modal -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="add_student.php" method="post">

                            <div class="form-group">
                                <label for="studentId">Student ID:</label>
                                <input type="text" class="form-control" id="studentId" name="studentId" pattern="[0-9]+" maxlength="7" required>
                                <small class="form-text text-muted" id="studentIdMessage">Please enter only numeric values, and the length must be 7.</small>
                            </div>
                            <div class="form-group">
                                <label for="studentName">Student name:</label>
                                <input type="text" class="form-control" id="studentName" name="studentName" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="addStudentBtn" disabled>Confirm</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="container mt-5">
            <div class="row">

                <div class="input-group py-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <button class="btn btn-primary" id="searchButton" type="button">Search</button>
                    <button class="btn btn-success" id="clearButton" type="button">Refresh</button>

                </div>

                <div class="col-lg-12">

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Student Id</th>
                                <th scope="col">Student Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paginatedResults as $row) : ?>
                                <tr data-student-id="<?= $row['student_id'] ?>">
                                    <td class="studentId"><?= $row['student_id'] ?></td>
                                    <td class="studentName"><?= $row['name'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>


                </div>

            </div>
        </div>


    </div>

    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <p><strong>Student Id: </strong><span id="modalStudentId"></span></p>
                        <p><strong>Name: </strong><span id="modalStudentName"></span></p>
                        <p><strong>Last ticket request: </strong><span id="modalRequestTimestamp"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="deleteStudentButton" class="btn btn-danger">Delete Student</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmDeletionModal" tabindex="-1" aria-labelledby="confirmDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeletionModalLabel">Confirm Student Deletion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <p class="text-center"><strong>Confirm Student deletion?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" id="confirmStudentDeletionBtn">Confirm</a>
                    <a class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            $('table tbody tr').on('click', function() {
                var studentId = $(this).find('.studentId').text();
                var studentName = $(this).find('.studentName').text();
                $.ajax({
                    type: 'GET',
                    url: 's_script/get_last_student_ticket_request.php',
                    dataType: 'json',
                    data: {
                        studentId: studentId
                    },
                    success: function(data) {
                        if (data.error) {
                            console.log('Error:', data.error);
                            $('#modalStudentId').text(studentId);
                            $('#modalStudentName').text(studentName);
                            $('#modalRequestTimestamp').text('Has not made a ticket request');
                            $('#studentDetailsModal').modal('show');
                        } else {
                            var requestTimestamp = data.request_timestamp;

                            $('#modalStudentId').text(studentId);
                            $('#modalStudentName').text(studentName);
                            $('#modalRequestTimestamp').text(requestTimestamp);

                            $('#studentDetailsModal').modal('show');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });
            });


            $(document).on('click', '#deleteStudentButton', function() {

                $('#studentDetailsModal').modal('hide');
                $('#confirmDeletionModal').modal('show');


            });

            $(document).on('click', '#confirmStudentDeletionBtn', function() {
                var studentId = $('#modalStudentId').text();

                var deleteStudentData = {
                    studentId: studentId
                };

                $.ajax({
                    type: 'POST',
                    url: 'delete_student.php',
                    data: deleteStudentData,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            $('#confirmDeletionModal').modal('hide');
                            $('#studentDetailsModal').modal('hide');
                            $('#successModal').modal('show');
                        } else {
                            alert('Failed to delete student.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });

            });

            var studentIdInput = $('#studentId');
            var studentIdMessage = $('#studentIdMessage');
            var addStudentBtn = $('#addStudentBtn');

            studentIdInput.on('keypress', function(event) {
                var charCode = event.which;

                //only nums
                if (charCode < 48 || charCode > 57) {
                    event.preventDefault();
                }
            });

            studentIdInput.on('keyup', function() {
                var studentId = studentIdInput.val();

                //check if input legth is 7
                if (studentId.length !== 7) {
                    studentIdMessage.text('The length must be 7.').show();
                    addStudentBtn.prop('disabled', true);
                    return;
                } else {
                    studentIdMessage.hide();
                }

                $.ajax({
                    type: 'POST',
                    url: 's_script/new_student_id_verify.php',
                    data: {
                        studentId: studentId
                    },
                    success: function(response) {
                        if (response.exists) {
                            studentIdMessage.text('Student ID already exists.').show();
                            addStudentBtn.prop('disabled', true);
                        } else {
                            studentIdMessage.hide();
                            addStudentBtn.prop('disabled', false);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });
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

            $('#okButton').on('click', function() {
                location.reload();
            });


        });
    </script>
</body>

</html>