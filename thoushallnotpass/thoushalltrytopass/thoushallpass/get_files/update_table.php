<?php
include '../../../../database_config/db_config.php';

$searchTerm = $_POST['searchTerm'];

date_default_timezone_set('Asia/Manila');
$currentTimestamp = date('Y-m-d H:i:s');

$sql = "
SELECT ticket, bus_plate_number, stop, student_id, fare, booked_at, status
FROM bookings
WHERE status = 'unpaid' && ticket_expiration_timestamp > :currentTimestamp
AND (ticket LIKE :searchTerm OR bus_plate_number LIKE :searchTerm OR stop LIKE :searchTerm OR student_id LIKE :searchTerm)
ORDER BY booked_at DESC;
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':currentTimestamp' => $currentTimestamp, ':searchTerm' => "%$searchTerm%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        echo '<tr data-ticket-code="' . $row['ticket'] . '">';
        echo '<td class="ticket">' . $row['ticket'] . '</td>';
        echo '<td class="bus_plate_number">' . $row['bus_plate_number'] . '</td>';
        echo '<td class="stop">' . $row['stop'] . '</td>';
        echo '<td class="student_id">' . $row['student_id'] . '</td>';
        echo '<td class="fare">' . $row['fare'] . '</td>';
        echo '<td class="booked_at">' . $row['booked_at'] . '</td>';
        echo '<td class="status">' . $row['status'] . '</td>';
        echo '</tr>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
