<?php
header('Content-Type: application/json');
include '../../../../database_config/db_config.php';
include '../../../../time/time_conf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $currentDateTime = date('Y-m-d H:i:s');

    try {
        //get json from post
        $ticketDataJson = $_POST['ticketData'];
        //decode json to array
        $ticketData = json_decode($ticketDataJson, true);

        //process ticket time
        foreach ($ticketData as &$ticket) {
            $ticketCode = $ticket['ticketCode'];
            $busPlateNumber = $ticket['busPlateNumber'];
            $studentId = $ticket['schoolId'];
            $stop = $ticket['stop'];
            $fare = $ticket['fare'];
            $isStudentVerified = $ticket['isStudentVerified'];
            $lastRequestStatus = $ticket['lastBookingStatus'];

            $transactionCode = "";

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
                    }


                    $userToken = 'Cashier';
                    $transactionCode = generateUniqueTransactionCode();
                    $ticket['transactionCode'] = $transactionCode;
                    $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    $status = 'paid';

                    //insert into bookings table
                    $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, booked_at, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id, :fare, :booked_at, :status, :paid_at)";
                    $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                    $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                    $insertBookingStmt->bindParam(':ticket', $ticketCode);
                    $insertBookingStmt->bindParam(':user_token', $userToken);
                    $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                    $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $insertBookingStmt->bindParam(':stop', $stop);
                    $insertBookingStmt->bindParam(':student_id', $studentId);
                    $insertBookingStmt->bindParam(':fare', $fare);
                    $insertBookingStmt->bindParam(':booked_at', $currentDateTime);
                    $insertBookingStmt->bindParam(':status', $status);
                    $insertBookingStmt->bindParam(':paid_at', $currentDateTime);

                    if ($insertBookingStmt->execute()) {

                        //we update existing student record with this:
                        $updateTicketRequestQuery = "UPDATE ticket_requests SET request_timestamp = :currentDateTime WHERE student_id = :studentId";
                        $requestStmt = $pdo->prepare($updateTicketRequestQuery);
                        $requestStmt->bindParam(':currentDateTime', $currentDateTime);
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
                    }


                    // if the student is requesting for the first time
                    $insertTicketRequestQuery = "INSERT INTO ticket_requests (student_id, request_timestamp) VALUES (:studentId, :currentDateTime)";
                    $requestStmt = $pdo->prepare($insertTicketRequestQuery);
                    $requestStmt->bindParam(':studentId', $studentId);
                    $requestStmt->bindParam(':currentDateTime', $currentDateTime);
                    $requestStmt->execute();

                    $userToken = 'Cashier';
                    $transactionCode = generateUniqueTransactionCode();
                    $ticket['transactionCode'] = $transactionCode;
                    $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));
                    $status = 'paid';

                    //insert into bookings table
                    $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, booked_at, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id,  :fare, :booked_at, :status, :paid_at)";
                    $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                    $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                    $insertBookingStmt->bindParam(':ticket', $ticketCode);
                    $insertBookingStmt->bindParam(':user_token', $userToken);
                    $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                    $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                    $insertBookingStmt->bindParam(':stop', $stop);
                    $insertBookingStmt->bindParam(':student_id', $studentId);
                    $insertBookingStmt->bindParam(':fare', $fare);
                    $insertBookingStmt->bindParam(':booked_at', $currentDateTime);
                    $insertBookingStmt->bindParam(':status', $status);
                    $insertBookingStmt->bindParam(':paid_at', $currentDateTime);

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
                }

                $userToken = 'Cashier';
                $transactionCode = generateUniqueTransactionCode();
                $ticket['transactionCode'] = $transactionCode;
                $status = 'paid';

                $insertBookingQuery = "INSERT INTO bookings (transaction_code, ticket, user_token, bus_plate_number, stop, student_id, fare, booked_at, status, paid_at) VALUES (:transaction_code, :ticket, :user_token, :bus_plate_number, :stop, :student_id, :fare, :booked_at, :status, :paid_at)";
                $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                $insertBookingStmt->bindParam(':transaction_code', $transactionCode);
                $insertBookingStmt->bindParam(':ticket', $ticketCode);
                $insertBookingStmt->bindParam(':user_token', $userToken);
                $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNumber);
                $insertBookingStmt->bindParam(':stop', $stop);
                $insertBookingStmt->bindParam(':student_id', $studentId);
                $insertBookingStmt->bindParam(':fare', $fare);
                $insertBookingStmt->bindParam(':booked_at', $fare);
                $insertBookingStmt->bindParam(':status', $status);
                $insertBookingStmt->bindParam(':paid_at', $fare);

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
                } else {
                    echo json_encode(['error' => 'Insertion Failed']);
                }
            }
        }
        unset($ticket);
        $response = ['success' => true, 'ticketData' => $ticketData];
        echo json_encode($response);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

function generateUniqueTransactionCode()
{
    include '../../../../database_config/db_config.php';

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
