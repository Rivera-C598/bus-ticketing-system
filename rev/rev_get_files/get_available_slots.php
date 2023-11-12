<?php
$busId = $_GET['busId'];

include '../../database_config/db_config.php';

$query = "SELECT confirmed_tickets, capacity FROM buses WHERE bus_id = :busId";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':busId', $busId, PDO::PARAM_INT);
$stmt->execute();
$busDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if ($busDetails) {
    $confirmedTickets = $busDetails['confirmed_tickets'];
    $capacity = $busDetails['capacity'];

    $availableSlots = $capacity - $confirmedTickets;

    header('Content-Type: application/json');
    echo json_encode(array('availableSlots' => $availableSlots));
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Bus not found'));
}
