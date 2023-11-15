<?php
include '../../database_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //get bus id
    $busId = $_POST['busId'];
    //get bus details based on bus id
    $query = "SELECT * FROM buses WHERE bus_id = :busId";
    $selectStmt = $pdo->prepare($query);
    $selectStmt->bindParam(':busId', $busId);
    $selectStmt->execute();

    $busDetails = $selectStmt->fetch(PDO::FETCH_ASSOC);

    if ($busDetails) {
        $busPlateNum = $busDetails['plate_number'];
        $capacity = $busDetails['capacity'];
        $confirmedTickets = $busDetails['confirmed_tickets'];
        $isAirConditioned = $busDetails['air_conditioned'];
    }

    //get form data
    $studentId = $_POST['studentId'];
    $stop = $_POST['busStop'];
    $fare = $_POST['fare'];

    //define fares for sserver side verification
    $baseFares = [
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

    // Adjust fares based on air-conditioning
    if ($isAirConditioned) {
        foreach ($baseFares as &$baseFare) {
            $baseFare += 10.00; // Add 10.00 to each base fare
        }
    }



    if ($confirmedTickets < $capacity) {
        if ($busDetails['status'] == "available") {
            //bus is available, perform important stuff
            //verify student ID
            $query = "SELECT * FROM student_reference WHERE student_id = :studentId";
            $studentStmt = $pdo->prepare($query);
            $studentStmt->bindParam(':studentId', $studentId);
            $studentStmt->execute();
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                // check if the student has made a request within the last 1 hours
                // perform a query to get student's last request:
                $cooldownQuery = "SELECT MAX(request_timestamp) AS last_request FROM ticket_requests WHERE student_id = :studentId";
                $cooldownStmt = $pdo->prepare($cooldownQuery);
                $cooldownStmt->bindParam(':studentId', $studentId);
                $cooldownStmt->execute();
                $lastRequest = $cooldownStmt->fetch(PDO::FETCH_ASSOC)['last_request'];

                // set timezone to asia/manila 
                date_default_timezone_set('Asia/Manila');

                if (!$lastRequest) {
                    $insertTicketRequestQuery = "INSERT INTO ticket_requests (student_id, request_timestamp) VALUES (:studentId, NOW())";
                    $requestStmt = $pdo->prepare($insertTicketRequestQuery);
                    $requestStmt->bindParam(':studentId', $studentId);
                    $requestStmt->execute();
                    // then we proceed to the bookigns
                    if (array_key_exists($stop, $baseFares) && $baseFares[$stop] == $fare) {
                        //generate a unique ticket code
                        $ticketCode = generateUniqueTicketCode();
                        $status = 'unpaid';

                        //create user token and ticket expiration date
                        $userToken = uniqid();
                        $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));

                        //snsert into bookings table
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
                        // invalid fare
                        header("Location: ../../info/error_page.php?error=invalid_fare");
                    }
                } else {
                    // if there is a record, we check the student's last request if its higher than the cooldown record, 
                    //if it is then we update new record

                    // step 1: calculate cooldown time
                    $currentTimestamp = time();
                    $lastRequestTimestamp = strtotime($lastRequest);
                    $timeElapsed = $currentTimestamp - $lastRequestTimestamp;
                    //set cooldown time
                    $cooldownPeriod = 10 * 60; //10 min cooldown (example onli) default should be 2 * 60 * 60 (2 hours)
                    // step 2: check if timeElapsed >= cooldownPeriod (timeElapsed should be greater than the cooldown time so that the student can request another ticket again))
                    if ($timeElapsed >= $cooldownPeriod) {
                        // If cooldown period has passed, we proceed with new booking

                        if (array_key_exists($stop, $baseFares) && $baseFares[$stop] == $fare) {
                            //generate ticket
                            $ticketCode = generateUniqueTicketCode();
                            $status = 'unpaid';

                            //create user token and ticket expiration date
                            $userToken = uniqid();
                            $ticketExpirationTimestamp = date('Y-m-d H:i:s', strtotime('+2 hours'));

                            //insert into bookings table
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

                                //we update existing student record with this:
                                $updateTicketRequestQuery = "UPDATE ticket_requests SET request_timestamp = NOW() WHERE student_id = :studentId";
                                $requestStmt = $pdo->prepare($updateTicketRequestQuery);
                                $requestStmt->bindParam(':studentId', $studentId);
                                $requestStmt->execute();

                                header("Location: ticket.php?token=" . $userToken);
                            } else {
                                echo "Insertion into 'bookings' table failed. Error: " . $insertBookingStmt->errorInfo()[2];
                            }
                        } else {
                            header("Location: ../../info/error_page.php?error=invalid_fare");
                        }
                    } else {
                        header("Location: ../../info/cooldown.php");
                    }
                }
            } else {
                header("Location: ../../info/error_page.php?error=invalid_studentId");
            }
        } elseif ($busDetails['status'] == "full") {
            $busStatus = $busDetails['status'];
            header("Location: ../route_selection.php?status=" . urlencode($busStatus));
        } else {
            $busStatus = $busDetails['status'];
            header("Location: ../route_selection.php?status=" . urlencode($busStatus));
        }
    } elseif ($confirmedTickets == $capacity) {

        $updateBusStatusOnTheGo = "UPDATE buses SET status = 'full' WHERE bus_id = :busId";
        $update = $pdo->prepare($updateBusStatusOnTheGo);
        $update->bindParam(':busId', $busId);
        $update->execute();

        $getStatus = "SELECT status FROM buses WHERE bus_id = :busId";
        $getStmt = $pdo->prepare($getStatus);
        $getStmt->bindParam(':busId', $busId);
        $getStmt->execute();

        $result = $getStmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $currentBusStatus = $result['status'];
        }

        header("Location: ../route_selection.php?status=" . urlencode($currentBusStatus));
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
    include '../../database_config/db_config.php';

    //check if $code exists in the bookings table
    $query = "SELECT COUNT(*) as count FROM bookings WHERE ticket = :code";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'] > 0;
}
