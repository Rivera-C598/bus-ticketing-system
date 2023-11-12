<?php

include '../../../../database_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $studentId = $_POST['studentId'];
    $sql = "DELETE FROM student_reference WHERE student_id = :studentId;";
    $sql2 = "DELETE FROM ticket_requests WHERE student_id = :studentId;";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':studentId', $studentId);

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':studentId', $studentId);

        if ($stmt->execute() && $stmt2->execute()) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error');
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}
