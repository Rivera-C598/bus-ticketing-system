<?php
include 'db_config.php';

// get token from url
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    //check if token exists in tickets table
    $query = "SELECT * FROM bookings WHERE user_token = :token AND status = 'unpaid'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        //check if token not expired
        date_default_timezone_set('Asia/Manila');
        $expirationTimestamp = $ticket['ticket_expiration_timestamp'];
        $currentTimestamp = date('Y-m-d H:i:s');

        if ($currentTimestamp < $expirationTimestamp) {
            //oki lezgayr
            $ticketCode = $ticket['ticket'];

            // Display the ticket information to the user
            $title = 'Bus Ticket';
            $busPlateNumber = $ticket['bus_plate_number'];
            $stop = $ticket['stop'];
            $fare = $ticket['fare'];
            $note = 'Instructions:';
            $instructions1 = 'This ticket serves as a reference. Please present this ticket to the bus ticketing cashier to pay the fare and obtain a bus ride ticket.';
            $instructions2 = 'Kindly note that this ticket will expire in 2 hours from the time of issuance.';
            $instructions3 = '';


        } else {
            $title = 'TICKET EXPIRED';
            $ticketCode = 'TICKET EXPIRED';
            $stop = 'TICKET EXPIRED';
            $fare = 0;
            $note = 'Note:';
            $instructions1 = 'Uh-oh, it seems your ticket has decided to take an early retirement and is no longer valid. You have two options at this point:';
            $instructions2 = 'You can request a new ticket by following the standard procedure.';
            $instructions3 = 'Alternatively, you may visit the bus ticket cashier to request a new ticket. They will assist you in obtaining a new one.';
            $expirationTimestamp = '';
        }
    } else {
        $title = 'ERROR';
        $ticketCode = 'ERROR';
        $stop = 'ERROR';
        $fare = 0;
        $note = 'Note:';
        $instructions1 = 'Ticket not found, or ticket has been paid';
        $instructions2 = '';
        $instructions3 = '';
        $expirationTimestamp = '';
    }
} else {
    echo "Token not found in the URL.";
}

/*
 $title = 'TICKET PAID';
        $ticketCode = 'TICKET PAID';
        $stop = 'TICKET PAID';
        $fare = 0;
        $note = 'Note:';
        $instructions1 = 'Congratulations! Your ticket has been paid successfully, and you should now have your bus ride ticket in hand as proof of payment. We truly appreciate your patience and cooperation.';
        $instructions2 = '';
        $instructions3 = '';
        $expirationTimestamp = '';
*/
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .ticket {
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            padding: 20px;
            max-width: 300px;
            margin: 0 auto;
            text-align: center;
        }

        .ticket-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .ticket-title {
            font-size: 20px;
            font-weight: bold;
        }

        .ticket-details {
            margin: 10px 0;
        }

        .ticket-code {
            font-size: 24px;
        }

        .ticket-info {
            font-size: 16px;
        }

        .ticket-instructions {
            font-size: 14px;
            text-align: left;
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="ticket-header">
            <div class="ticket-title"><?php echo $title; ?></div>
        </div>
        <div class="ticket-details">
            <div class="ticket-code"><?php echo $ticketCode; ?></div>
            <div class="ticket-info">Stop: <?php echo $stop; ?></div>
            <div class="ticket-info">Fare: Php <?php echo number_format($fare, 2); ?></div>
        </div>
        <div class="ticket-instructions">
            <p><strong><?php echo $note; ?></strong></p>
            <p><?php echo $instructions1; ?></p>
            <p><?php echo $instructions2; ?></p>
            <p><?php echo $instructions3; ?></p>
            <p>Expiration: <?php echo date('F j, Y h:i A', strtotime($expirationTimestamp)); ?></p>
        </div>
    </div>
</body>

</html>