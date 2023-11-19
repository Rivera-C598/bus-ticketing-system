<?php
if (isset($_GET['status'])) {
    $busStatus = $_GET['status'];
    if ($busStatus == 'full') {
        $redirectInformation = "We apologize for the inconvenience. The selected bus is already full. Please do consider booking another bus below. Thank you for your understanding.";
    } else {
        $redirectInformation = "We apologize for the inconvenience. Tickets for this bus are temporarily closed due to some reasons. Please do consider booking another bus below. Thank you for your understanding.";
    }
} else {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/ticketing_styles.css">

    <title>Bus Ticketing System - Route Selection Page</title>
    <style>
        .message {
            font-size: 14px;
            text-align: left;
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .container {
            margin-top: 20px;
        }

        .btn {
            margin: 5px;
        }

        .label {
            margin-top: 10px;
        }

        .wrapper {
            background: rgba(255, 255, 255, 0.19);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }



        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: url("../img_assets/web-bg.jpg") center no-repeat;
            background-size: cover;

        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php if (isset($_GET['status'])) : ?>
            <div class="message border border-danger text-danger bg-light">
                <p><?php echo $redirectInformation; ?></p>
            </div>
        <?php endif; ?>

        <label class="display-6 text-center"><strong>Choose a route</strong></label>

        <div class="container">
            <a href="available_buses.php?route=Going North&status=available" class="btn btn-primary">Going North</a>
            <a href="available_buses.php?route=Going South&status=available" class="btn btn-primary">Going South</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>