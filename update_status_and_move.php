<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket']) && isset($_POST['busPlateNumber'])) {
    $ticket = $_POST['ticket'];
    $busPlateNumber = $_POST['busPlateNumber'];

    //check if theres available capacity on the bus
    $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND status = 'available' OR status = 'full'";
    $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
    $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
    $checkCapacityStmt->execute();
    $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

    if ($busData) {
        $capacity = $busData['capacity'];
        $confirmedTickets = $busData['confirmed_tickets'];
        $busStatus = $busData['status'];

        if ($busStatus === 'full') {
            //if bus full, edi full
            echo 'error: Bus is full';
        } else {
            if ($confirmedTickets < $capacity) {
                $updateSql = "UPDATE buses SET confirmed_tickets = confirmed_tickets + 1 WHERE plate_number = :bus_plate_number";
            } else {
                $updateSql = "UPDATE buses SET status = 'full' WHERE plate_number = :bus_plate_number";
            }

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);

            try {
                $pdo->beginTransaction();
                $updateStmt->execute();
                $pdo->commit();

                $transactionCode = generateUniqueTransactionCode();

                $updateBookingSql = "UPDATE bookings SET status = 'paid', transaction_code = :transaction_code, paid_at = NOW() WHERE ticket = :ticket";
                $updateBookingStmt = $pdo->prepare($updateBookingSql);
                $updateBookingStmt->bindParam(':ticket', $ticket);
                $updateBookingStmt->bindParam(':transaction_code', $transactionCode);

                $pdo->beginTransaction();
                $updateBookingStmt->execute();
                $pdo->commit();

                echo 'success';
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo 'error: Failed to update status';
            }
        }
    } else {
        echo 'error: Bus not found';
    }
}



function generateUniqueTransactionCode()
{
    include 'db_config.php';

    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    do {
        $transactionCode = '';
        for ($i = 0; $i < 12; $i++) {
            $transactionCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        $checkSql = "SELECT COUNT(*) AS count FROM bookings WHERE transaction_code = :transaction_code AND status = 'paid'";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':transaction_code', $transactionCode);
        $checkStmt->execute();
        $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
    } while ($count > 0);

    return $transactionCode;
}
