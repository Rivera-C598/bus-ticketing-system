<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busId = $_POST['busId'];
    $query = "SELECT * FROM buses WHERE bus_id = :busId";
    $selectStmt = $pdo->prepare($query);
    $selectStmt->bindParam(':busId', $busId);
    $selectStmt->execute();

    $busDetails = $selectStmt->fetch(PDO::FETCH_ASSOC);

    if ($busDetails) {
        $busPlateNum = $busDetails['plate_number'];
        $capacity = $busDetails['capacity'];
    }

    $studentId = $_POST['studentId'];
    $stop = $_POST['busStop'];
    $fare = $_POST['fare'];

    $fares = [
        'Compostela' => 10.00,
        'Liloan' => 20.00,
        'Consolacion' => 30.00,
        'Mandaue' => 40.00,
        'Cebu' => 50.00,
        'Danao City' => 10.00,
        'Carmen' => 20.00,
        'Catmon' => 30.00,
        'Sogod' => 40.00
    ];

    // Verify student ID
    $query = "SELECT * FROM student_reference WHERE student_id = :studentId";
    $studentStmt = $pdo->prepare($query);
    $studentStmt->bindParam(':studentId', $studentId);
    $studentStmt->execute();
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $cooldownQuery = "SELECT MAX(request_timestamp) AS last_request FROM ticket_requests WHERE student_id = :studentId";
        $cooldownStmt = $pdo->prepare($cooldownQuery);
        $cooldownStmt->bindParam(':studentId', $studentId);
        $cooldownStmt->execute();
        $lastRequest = $cooldownStmt->fetch(PDO::FETCH_ASSOC)['last_request'];

        date_default_timezone_set('Asia/Manila');

        if (!$lastRequest) {
            $insertTicketRequestQuery = "INSERT INTO ticket_requests (student_id, request_timestamp) VALUES (:studentId, NOW())";
            $requestStmt = $pdo->prepare($insertTicketRequestQuery);
            $requestStmt->bindParam(':studentId', $studentId);
            $requestStmt->execute();
            if (array_key_exists($stop, $fares) && $fares[$stop] == $fare) {
                $ticketCode = generateUniqueTicketCode();
                $status = 'unpaid';

                $userToken = uniqid();
                $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));

                // Insert into bookings table
                $insertBookingQuery = "INSERT INTO bookings (ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, status) VALUES (:ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id, :fare, :status)";
                $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                $insertBookingStmt->bindParam(':ticket', $ticketCode);
                $insertBookingStmt->bindParam(':user_token', $userToken);
                $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNum);
                $insertBookingStmt->bindParam(':stop', $stop);
                $insertBookingStmt->bindParam(':student_id', $studentId);
                $insertBookingStmt->bindParam(':fare', $fare);
                $insertBookingStmt->bindParam(':status', $status);

                if ($insertBookingStmt->execute()) {
                    header("Location: ticket.php?token=" . $userToken);
                } else {
                    echo "Insertion into 'bookings' table failed. Error: " . $insertBookingStmt->errorInfo()[2];
                }
            } else {
                echo 'Invalid fare. Please check the selected bus stop and fare.';
            }
        } else {
            // if there is a record, we check student's last request if its higher than the cooldown record, 
            //if it is then we update new record

            // step 1: calculate cooldown time
            $currentTimestamp = time();
            $lastRequestTimestamp = strtotime($lastRequest);
            $timeElapsed = $currentTimestamp - $lastRequestTimestamp;
            $cooldownPeriod = 1 * 60; 
            if ($timeElapsed >= $cooldownPeriod) {
                $updateTicketRequestQuery = "UPDATE ticket_requests SET request_timestamp = NOW() WHERE student_id = :studentId";
                $requestStmt = $pdo->prepare($updateTicketRequestQuery);
                $requestStmt->bindParam(':studentId', $studentId);
                $requestStmt->execute();
                
                if (array_key_exists($stop, $fares) && $fares[$stop] == $fare) {
                    
                    $ticketCode = generateUniqueTicketCode();
                    $status = 'unpaid';

                    $userToken = uniqid();
                    $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));

                    $insertBookingQuery = "INSERT INTO bookings (ticket, user_token, ticket_expiration_timestamp, bus_plate_number, stop, student_id, fare, status) VALUES (:ticket, :user_token, :ticket_expiration_timestamp, :bus_plate_number, :stop, :student_id, :fare, :status)";
                    $insertBookingStmt = $pdo->prepare($insertBookingQuery);
                    $insertBookingStmt->bindParam(':ticket', $ticketCode);
                    $insertBookingStmt->bindParam(':user_token', $userToken);
                    $insertBookingStmt->bindParam(':ticket_expiration_timestamp', $ticketExpirationTimestamp);
                    $insertBookingStmt->bindParam(':bus_plate_number', $busPlateNum);
                    $insertBookingStmt->bindParam(':stop', $stop);
                    $insertBookingStmt->bindParam(':student_id', $studentId);
                    $insertBookingStmt->bindParam(':fare', $fare);
                    $insertBookingStmt->bindParam(':status', $status);

                    if ($insertBookingStmt->execute()) {
                        header("Location: ticket.php?token=" . $userToken);
                    } else {
                        echo "Insertion into 'bookings' table failed. Error: " . $insertBookingStmt->errorInfo()[2];
                    }
                } else {
                    echo 'Invalid fare. Please check the selected bus stop and fare.';
                }
            } else {
                header("Location: cooldown.php");
            }
        }
    } else {
        echo 'Invalid student ID. Please check the entered student ID.';
    }
}







function generateUniqueTicketCode()
{
    $characters = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $codeLength = 8;

    do {
        $ticketCode = '';

        for ($i = 0; $i < $codeLength; $i++) {
            $ticketCode .= $characters[rand(0, count($characters) - 1)];
        }

        $codeExistsInBookings = checkCodeInBookings($ticketCode);
    } while ($codeExistsInBookings);

    return $ticketCode;
}

function checkCodeInBookings($code)
{
    include 'db_config.php';

    // Check if $code exists in the bookings table
    $query = "SELECT COUNT(*) as count FROM bookings WHERE ticket = :code";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'] > 0;
}
