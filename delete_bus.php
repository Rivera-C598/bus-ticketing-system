<?php

include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $busId = $_POST['busId'];
        $sql = "DELETE FROM buses WHERE bus_id = :busId;";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busId', $busId);

        if ($stmt->execute()) {
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
