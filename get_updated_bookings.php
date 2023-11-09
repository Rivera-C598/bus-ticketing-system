<?php
include 'db_config.php';
date_default_timezone_set('Asia/Manila');
$currentTimestamp = date('Y-m-d H:i:s');

$sql = "SELECT ticket, bus_plate_number, stop, student_id, fare, booked_at, status FROM bookings WHERE status = 'unpaid' && ticket_expiration_timestamp > :currentTimestamp ORDER BY booked_at DESC;";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':currentTimestamp' => $currentTimestamp]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results); 
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
