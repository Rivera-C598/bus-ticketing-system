<?php
include 'db_config.php';

$sql = "SELECT transaction_code, ticket, bus_plate_number, student_id, stop, fare, booked_at, paid_at, user_token, ticket_expiration_timestamp FROM bookings WHERE status = 'paid' ORDER BY paid_at DESC;";
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo "<tr data-transaction-code=\"{$row['transaction_code']}\">";
    echo "<td>{$row['transaction_code']}</td>";
    echo "<td>{$row['ticket']}</td>";
    echo "<td>{$row['bus_plate_number']}</td>";
    echo "<td>{$row['student_id']}</td>";
    echo "<td>{$row['user_token']}</td>";
    echo "<td>{$row['stop']}</td>";
    echo "<td>{$row['fare']}</td>";
    echo "<td>{$row['booked_at']}</td>";
    echo "<td>{$row['paid_at']}</td>";
    echo "</tr>";
}
