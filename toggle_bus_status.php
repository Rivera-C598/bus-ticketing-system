<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plateNumber = $_POST['busPlateNum'];
    $oldStatus = $_POST['oldStatus'];
    $newStatus = $_POST['newStatus'];

    if ($oldStatus == 'unavailable' && $newStatus == 'available') {

        $unToAUpdateQuery = "UPDATE buses SET confirmed_tickets = 0, status = :status WHERE plate_number = :plateNumber";
        $unToAStmt = $pdo->prepare($unToAUpdateQuery);
        $unToAStmt->bindValue(':plateNumber', $plateNumber);
        $unToAStmt->bindValue(':status', $newStatus);
        if ($unToAStmt->execute()) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error');
            echo json_encode($response);
        }
    }

    if ($oldStatus == 'full' && $newStatus == 'available') {

        $unToAUpdateQuery = "UPDATE buses SET confirmed_tickets = 0, status = :status WHERE plate_number = :plateNumber";
        $unToAStmt = $pdo->prepare($unToAUpdateQuery);
        $unToAStmt->bindValue(':plateNumber', $plateNumber);
        $unToAStmt->bindValue(':status', $newStatus);
        if ($unToAStmt->execute()) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error');
            echo json_encode($response);
        }
    }
    if ($oldStatus == 'full' && $newStatus == 'unavailable') {

        $unToAUpdateQuery = "UPDATE buses SET confirmed_tickets = 0, status = :status WHERE plate_number = :plateNumber";
        $unToAStmt = $pdo->prepare($unToAUpdateQuery);
        $unToAStmt->bindValue(':plateNumber', $plateNumber);
        $unToAStmt->bindValue(':status', $newStatus);
        if ($unToAStmt->execute()) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error');
            echo json_encode($response);
        }
    }

    if ($oldStatus == 'available' && $newStatus == 'unavailable') {

        $unToAUpdateQuery = "UPDATE buses SET confirmed_tickets = 0, status = :status WHERE plate_number = :plateNumber";
        $unToAStmt = $pdo->prepare($unToAUpdateQuery);
        $unToAStmt->bindValue(':plateNumber', $plateNumber);
        $unToAStmt->bindValue(':status', $newStatus);
        if ($unToAStmt->execute()) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error');
            echo json_encode($response);
        }
    }

    if ($oldStatus == 'available' && $newStatus == 'full') {

        $query = "SELECT * FROM buses WHERE plate_number = :plateNumber";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':plateNumber', $plateNumber);
        $stmt->execute();

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {

            $capacity = $ticket['capacity'];
            $unToAUpdateQuery = "UPDATE buses SET confirmed_tickets = :capacity, status = :status WHERE plate_number = :plateNumber";
            $unToAStmt = $pdo->prepare($unToAUpdateQuery);
            $unToAStmt->bindValue(':plateNumber', $plateNumber);
            $unToAStmt->bindValue(':capacity', $capacity);
            $unToAStmt->bindValue(':status', $newStatus);
            if ($unToAStmt->execute()) {
                $response = array('status' => 'success');
                echo json_encode($response);
            } else {
                $response = array('status' => 'error');
                echo json_encode($response);
            }
        }
    }
}
