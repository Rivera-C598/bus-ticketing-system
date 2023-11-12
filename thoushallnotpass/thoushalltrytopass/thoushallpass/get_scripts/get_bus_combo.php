<?php

include '../../../../database_config/db_config.php';

if (isset($_GET['busId'])) {
    $busId = $_GET['busId'];
    $sql = "SELECT bus_id, plate_number, bus_driver_name, busPhoto, capacity, air_conditioned FROM buses WHERE bus_id = :busId";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':busId' => $busId]);
        $busData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($busData) {
            //aclucate avialble slots
            $availableSlots = $busData['capacity'] - getConfirmedTickets($busId, $pdo);

            $busData['available_slots'] = $availableSlots;

            header('Content-Type: application/json');
            echo json_encode($busData);
        } else {
            echo 'Bus not found.';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'Bus ID parameter is missing.';
}

function getConfirmedTickets($busId, $pdo)
{
    $sql = "SELECT confirmed_tickets FROM buses WHERE bus_id = :busId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':busId' => $busId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return (int)$result['confirmed_tickets'];
    } else {
        return 0;
    }
}
