<?php
include '../../../../../database_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = isset($_POST['studentId']) ? $_POST['studentId'] : null;

    if ($studentId) {
        $query = "SELECT COUNT(*) as count FROM student_reference WHERE student_id = :studentId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = ['exists' => ($result['count'] > 0)];

        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
} else {
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>

