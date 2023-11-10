<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: admin_login.php");
    exit();
}

include 'db_config.php';

$query = "SELECT student_id, name FROM student_reference;";

try {
    $stmt = $pdo->prepare($query);
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

    <title>Student reference - Bus Ticketing System</title>
</head>

<body>
    <div class="wrapper">
        <header class="bg-primary text-white text-center py-5">

        </header>
        <main class="container my-5">
            <section id="manage-buses" class="mb-4 px-3">
                <div class="container text-center">
                    <h2>Students</h2>
                    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
                    <a href="admin_control_panel.php" class="btn btn-outline-primary btn-md">Control Panel</a>
                </div>
            </section>
        </main>
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
                            <input type="text" class="form-control" id="studentId" name="studentId" required>
                        </div>
                        <div class="form-group">
                            <label for="studentName">Student name:</label>
                            <input type="text" class="form-control" id="studentName" name="studentName" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Confirm</button>
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
            <h2 class="text-center">Student Reference Table</h2>

            <div class="input-group py-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
            </div>

            <div class="col-lg-12">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Student Id</th>
                            <th scope="col">Student Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row) : ?>
                            <tr data-student-id="<?= $row['student_id'] ?>">
                                <td class="studentId"><?= $row['student_id'] ?></td>
                                <td class="studentName"><?= $row['name'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


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
            
            function filterTable(searchValue) {
                $('table tbody tr').each(function() {
                    var row = $(this);

                    if (row.text().toLowerCase().includes(searchValue)) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            function updateTableData() {
                $.ajax({
                    type: 'GET',
                    url: 'get_students.php',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            console.log('Error:', data.error);
                        } else {
                            $('table tbody').empty();

                            $.each(data, function(index, row) {
                                var newRow = '<tr data-student-id="' + row.student_id + '">' +
                                    '<td class="studentId">' + row.student_id + '</td>' +
                                    '<td class="studentName">' + row.name + '</td>' +
                                    '</tr>';

                                $('table tbody').append(newRow);
                            });

                            var searchValue = $('#searchInput').val().toLowerCase();
                            filterTable(searchValue);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error: ' + textStatus, errorThrown);
                    }
                });
            }

            updateTableData();
            setInterval(updateTableData, 3000);

        });
    </script>
</body>

</html>