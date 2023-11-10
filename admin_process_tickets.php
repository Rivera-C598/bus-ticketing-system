<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        //get json from post
        $ticketDataJson = $_POST['ticketData'];

        //decode json to array
        $ticketData = json_decode($ticketDataJson, true);

        //process ticket time
        foreach ($ticketData as $ticket) {
            $ticketCode = $ticket['ticketCode'];
            $busPlateNumber = $ticket['busPlateNumber'];
            $studentId = $ticket['schoolId'];
            $stop = $ticket['stop'];
            $fare = $ticket['fare'];
            $isStudentVerified = $ticket['isStudentVerified'];
            $lastRequestStatus = $ticket['lastBookingStatus'];

            if ($isStudentVerified) {

                if ($lastRequestStatus == 'true') {
                    //check if there's available capacity on the bus
                    $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND (status = 'available' OR status = 'full')";
                    $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                    $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $checkCapacityStmt->execute();
                    $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                    if ($busData) {
                        $capacity = $busData['capacity'];
                        $confirmedTickets = $busData['confirmed_tickets'];
                        $busStatus = $busData['status'];
                    } else {
                        echo 'error: Bus not found';
                    }

                    if ($confirmedTickets < $capacity) {
                        $updateSql = "UPDATE buses SET confirmed_tickets = confirmed_tickets + 1 WHERE plate_number = :bus_plate_number";
                        echo "Still not full\n";
                    }


                    $userToken = 'Cashier';
                    $transactionCode = generateUniqueTransactionCode();
                    $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    $status = 'paid';

                    //insert into bookings table
                    $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id, :fare, :status, NOW())";
                    $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                    $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                    $insertBookingStmt->bindParam(':ticket', $ticketCode);
                    $insertBookingStmt->bindParam(':user_token', $userToken);
                    $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                    $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $insertBookingStmt->bindParam(':stop', $stop);
                    $insertBookingStmt->bindParam(':student_id', $studentId);
                    $insertBookingStmt->bindParam(':fare', $fare);
                    $insertBookingStmt->bindParam(':status', $status);

                    if ($insertBookingStmt->execute()) {

                        //we update existing student record with this:
                        $updateTicketRequestQuery = "UPDATE ticket_requests SET request_timestamp = NOW() WHERE student_id = :studentId";
                        $requestStmt = $pdo->prepare($updateTicketRequestQuery);
                        $requestStmt->bindParam(':studentId', $studentId);
                        $requestStmt->execute();

                        //update buses
                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                        $updateStmt->execute();

                        $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND status = 'available';";
                        $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                        $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                        $checkCapacityStmt->execute();
                        $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                        if ($busData) {
                            $capacity = $busData['capacity'];
                            $confirmedTickets = $busData['confirmed_tickets'];
                            $busStatus = $busData['status'];
                        } else {
                            echo 'error: Bus not found';
                        }

                        if ($confirmedTickets == $capacity) {
                            $updateSql = "UPDATE buses SET status = 'full' WHERE plate_number = :bus_plate_number";
                            $updateStmt = $pdo->prepare($updateSql);
                            $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                            $updateStmt->execute();
                        }

                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['error' => 'Insertion Failed']);
                    }
                } else {
                    //if the student first time requests but through cashier
                    //check if there's available capacity on the bus
                    $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND (status = 'available' OR status = 'full')";
                    $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                    $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $checkCapacityStmt->execute();
                    $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                    if ($busData) {
                        $capacity = $busData['capacity'];
                        $confirmedTickets = $busData['confirmed_tickets'];
                        $busStatus = $busData['status'];
                    } else {
                        echo 'error: Bus not found';
                    }

                    if ($confirmedTickets < $capacity) {
                        $updateSql = "UPDATE buses SET confirmed_tickets = confirmed_tickets + 1 WHERE plate_number = :bus_plate_number";
                        echo "Still not full\n";
                    }


                    // if the student is requesting for the first time
                    $insertTicketRequestQuery = "INSERT INTO ticket_requests (student_id, request_timestamp) VALUES (:studentId, NOW())";
                    $requestStmt = $pdo->prepare($insertTicketRequestQuery);
                    $requestStmt->bindParam(':studentId', $studentId);
                    $requestStmt->execute();

                    $userToken = 'Cashier';
                    $transactionCode = generateUniqueTransactionCode();
                    $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    $status = 'paid';

                    //insert into bookings table
                    $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id, :fare, :status, NOW())";
                    $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                    $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                    $insertBookingStmt->bindParam(':ticket', $ticketCode);
                    $insertBookingStmt->bindParam(':user_token', $userToken);
                    $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                    $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $insertBookingStmt->bindParam(':stop', $stop);
                    $insertBookingStmt->bindParam(':student_id', $studentId);
                    $insertBookingStmt->bindParam(':fare', $fare);
                    $insertBookingStmt->bindParam(':status', $status);

                    if ($insertBookingStmt->execute()) {

                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                        $updateStmt->execute();

                        $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND status = 'available';";
                        $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                        $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                        $checkCapacityStmt->execute();
                        $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                        if ($busData) {
                            $capacity = $busData['capacity'];
                            $confirmedTickets = $busData['confirmed_tickets'];
                            $busStatus = $busData['status'];
                        } else {
                            echo 'error: Bus not found';
                        }

                        if ($confirmedTickets == $capacity) {
                            $updateSql = "UPDATE buses SET status = 'full' WHERE plate_number = :bus_plate_number";
                            $updateStmt = $pdo->prepare($updateSql);
                            $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                            $updateStmt->execute();
                        }


                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['error' => 'Insertion Failed']);
                    }
                }
            } else {
                //if verification is turned off
                //check if there's available capacity on the bus
                $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND status = 'available';";
                $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                $checkCapacityStmt->execute();
                $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                if ($busData) {
                    $capacity = $busData['capacity'];
                    $confirmedTickets = $busData['confirmed_tickets'];
                    $busStatus = $busData['status'];
                } else {
                    echo 'error: Bus not found';
                }

                if ($confirmedTickets < $capacity) {
                    $updateSql = "UPDATE buses SET confirmed_tickets = confirmed_tickets + 1 WHERE plate_number = :bus_plate_number";
                    echo "Still not full\n";
                }

                $userToken = 'Cashier';
                $transactionCode = generateUniqueTransactionCode();
                $status = 'paid';

                $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, bus_plate_number, stop, student_id, fare, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :bus_plate_number, :stop, :student_id, :fare, :status, NOW())";
                $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                $insertBookingStmt->bindParam(':ticket', $ticketCode);
                $insertBookingStmt->bindParam(':user_token', $userToken);
                $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                $insertBookingStmt->bindParam(':stop', $stop);
                $insertBookingStmt->bindParam(':student_id', $studentId);
                $insertBookingStmt->bindParam(':fare', $fare);
                $insertBookingStmt->bindParam(':status', $status);

                if ($insertBookingStmt->execute()) {
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $updateStmt->execute();

                    $checkCapacitySql = "SELECT capacity, confirmed_tickets, status FROM buses WHERE plate_number = :bus_plate_number AND status = 'available';";
                    $checkCapacityStmt = $pdo->prepare($checkCapacitySql);
                    $checkCapacityStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $checkCapacityStmt->execute();
                    $busData = $checkCapacityStmt->fetch(PDO::FETCH_ASSOC);

                    if ($busData) {
                        $capacity = $busData['capacity'];
                        $confirmedTickets = $busData['confirmed_tickets'];
                        $busStatus = $busData['status'];
                    } else {
                        echo 'error: Bus not found';
                    }

                    if ($confirmedTickets == $capacity) {
                        $updateSql = "UPDATE buses SET status = 'full' WHERE plate_number = :bus_plate_number";
                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->bindParam(':bus_plate_number', $busPlateNumber);
                        $updateStmt->execute();
                    }
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Insertion Failed']);
                }
            }
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
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
