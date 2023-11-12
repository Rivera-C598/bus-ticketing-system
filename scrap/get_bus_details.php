<?php
include 'db_config.php';

if (isset($_GET['busId'])) {
    $busId = $_GET['busId'];

    $query = "SELECT * FROM buses WHERE bus_id = :busId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':busId', $busId);
    $stmt->execute();

    $busDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($busDetails) {
        $airConditioned = $busDetails['air_conditioned'];

        echo '<div class="bus-details-container">';
        echo '<div class="bus-photo-column">';
        echo '<img src="' . $busDetails['busPhoto'] . '" alt="Bus Photo" style="max-width: 200px; max-height: 150px;">';
        echo '</div>';
        echo '<div class="bus-info-column">';
        echo '<strong>Bus Plate Number:</strong> ' . $busDetails['plate_number'] . '<br>';
        echo '<strong>Driver Name:</strong> ' . $busDetails['bus_driver_name'] . '<br>';
        echo '<strong>Route:</strong> ' . $busDetails['route'] . '<br>';
        echo '<strong>Air Conditioned</strong> ' . ($airConditioned ? "Yes" : "No") . '<br>';

        echo '</div>';
        echo '</div>';
        echo '<form class="ticket-processing-form" action="ticket_form.php" method="post">';
        echo '<input type="hidden" id="busId" name="busId" value="' . $busId . '">';
        echo '<button type="submit" class="btn btn-primary" style="margin: 10px auto;">Book Bus</button>';
        echo '</form>';
    } else {
        echo 'Bus not found';
    }
} else {
    echo 'Invalid request';
}


?>
