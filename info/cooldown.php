<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .ticket {
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            padding: 20px;
            max-width: 500px;
            margin: 30px auto;
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
            <div class="ticket-title">Ticket Information</div>
        </div>
        <div class="ticket-details">
            <p>Hello! It appears you're in cooldown as you have recently requested a ticket within the past (2) hours.</p>
            <p>Remember, only one ticket request is allowed per student every (2) hours.</p>
        </div>
        <div class="ticket-instructions">
            <p><strong>Important Instructions:</strong></p>
            <p>- If you've forgotten your previous ticket code, don't worry! Head to the bus ticketing cashier and provide your ticket details.</p>
            <p>- Make sure to bring your school ID. If you forgot it, simply share your name and any other relevant information at the cashier.</p>
            <p>- No worries if you're feeling shy! You can bring a friend along to help plead your case. We're here to assist you, so don't hesitate to ask!</p>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</html>