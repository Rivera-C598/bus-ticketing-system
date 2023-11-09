<?php
include 'db_config.php';

if (isset($_GET['route'])) {
    $route = $_GET['route'];

    $sql = "SELECT bus_id, plate_number FROM buses WHERE route = :route AND status = 'available'";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':route' => $route]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($results);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'Route parameter is missing.';
}
