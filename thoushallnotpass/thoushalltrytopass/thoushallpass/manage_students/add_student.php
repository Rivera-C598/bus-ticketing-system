<?php
include '../../../../database_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = filter_input(INPUT_POST, 'studentId', FILTER_SANITIZE_SPECIAL_CHARS);
    $studentName = filter_input(INPUT_POST, 'studentName', FILTER_SANITIZE_SPECIAL_CHARS);

    $query = "INSERT INTO student_reference (student_id, name) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$studentId, $studentName])) {
        //student added scucessfully
        header("Location: students.php");
        exit();
    } else {
        //error adding student
        echo "Error adding student.";
    }
}
