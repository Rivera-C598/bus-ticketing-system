<?php
include '../../../../../database_config/db_config.php';

if (isset($_GET['studentId'])) {
    $studentId = $_GET['studentId'];

    $query = "SELECT request_timestamp FROM ticket_requests WHERE student_id = :studentId";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['request_timestamp' => $result['request_timestamp']]);
        } else {
            echo json_encode(['error' => 'No ticket request found for the student.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid parameters.']);
}
?>
