<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketing_form_style.css">

    <title>Bus Ticketing System - Book tickets</title>
</head>

<body>
    <div class="wrapper">
        <?php
        include 'db_config.php';

        if (isset($_GET['busId'])) {
            $busId = isset($_GET['busId']) ? $_GET['busId'] : '';

            $query = "SELECT * FROM buses WHERE bus_id = :busId";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':busId', $busId);
            $stmt->execute();

            $busDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($busDetails) {
                //bus details
                $airConditioned = $busDetails['air_conditioned'];
                echo '<div class="bus-details-container" style="font-size: 20px;">';
                echo '<div class="bus-photo-column">';
                echo '<img src="' . $busDetails['busPhoto'] . '" alt="Bus Photo" style="max-width: 200px; max-height: 150px;">';
                echo '</div>';
                echo '<div class="bus-info-column">';
                echo '<strong>Bus Plate Number:</strong> ' . $busDetails['plate_number'] . '<br>';
                echo '<strong>Driver Name:</strong> ' . $busDetails['bus_driver_name'] . '<br>';
                echo '<strong>Route:</strong> ' . $busDetails['route'] . '<br>';
                echo '<strong>Air Conditioned</strong> ' . ($airConditioned ? "Yes" : "No") . '<br>';
                echo '<strong>Available slots: </strong><span id="availableSlots">Loading...</span>';
                echo '</div>';
                echo '</div>';
                $airConditioned = ($busDetails['air_conditioned'] == 1) ? true : false;
                echo '<script>';
                echo 'var airConditioned = ' . json_encode($airConditioned) . ';';
                echo '</script>';

                //form for processing tickets
                echo '<form class="ticket-processing-form" action="process_tickets.php" method="post">';
                echo '<h3 class="text-center">Book Tickets</h3>';
                echo '<label for="studentId">Student ID:</label>';
                echo '<input type="text" id="studentId" name="studentId" required maxlength="7">';
                echo '<label for="busStop">Your Stop:</label>';
                echo '<select id="busStop" name="busStop" required>';
                echo '<option value="" disabled selected>Choose stop</option>'; //fdefault option
                if (strpos($busDetails['route'], 'North') !== false) {
                    echo '<option value="Danao City">Danao City</option>';
                    echo '<option value="Carmen">Carmen</option>';
                    echo '<option value="Catmon">Catmon</option>';
                    echo '<option value="Sogod">Sogod</option>';
                } elseif (strpos($busDetails['route'], 'South') !== false) {
                    echo '<option value="Compostela">Compostela</option>';
                    echo '<option value="Liloan">Liloan</option>';
                    echo '<option value="Consolacion">Consolacion</option>';
                    echo '<option value="Mandaue">Mandaue</option>';
                    echo '<option value="Cebu">Cebu</option>';
                }
                echo '</select>';
                echo '</select>';
                echo '<input type="hidden" id="busId" name="busId" value="' . $busId . '">';
                echo '<label for="fare">Fare:</label>';
                echo '<div class="input-group flex-nowrap">';
                echo '<div class="input-group-prepend">';
                echo '<span class="input-group-text" id="addon-wrapping">Php</span>';
                echo '</div>';
                echo '<input type="text" id="fare" name="fare" value="" readonly>';
                echo '</div>';
                echo '<button type="submit" class="btn btn-primary center-button">Confirm & submit</button>';
                echo '</form>';
            }
        } else {
            echo 'bus Id not found';
        }
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        function updateAvailableSlots() {
            var busId = $('#busId').val();
            $.ajax({
                url: 'get_available_slots.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    busId: busId
                },
                success: function(response) {
                    if (response.hasOwnProperty('availableSlots')) {
                        $('#availableSlots').text(response.availableSlots);
                    }
                },
                error: function() {
                    $('#availableSlots').text('N/A');
                }
            });
        }

        setInterval(updateAvailableSlots, 3000); //2 sekus update
    </script>

    <script>
        var busStopDropdown = document.getElementById('busStop');
        var fareInput = document.getElementById('fare');

        var fares = {
            'Compostela': 10.00,
            'Liloan': 20.00,
            'Consolacion': 30.00,
            'Mandaue': 40.00,
            'Cebu': 50.00,
            'Danao City': 10.00,
            'Carmen': 20.00,
            'Catmon': 30.00,
            'Sogod': 40.00
        };

        busStopDropdown.addEventListener('change', function() {
            var selectedOption = busStopDropdown.value;

            //dafault to nothin if value is nothin
            var baseFare = selectedOption === '' ? 0.00 : (fares[selectedOption] || 10.00);

            //if airConditioned true, add 10.00 to the base fare hehe
            var finalFare = airConditioned ? baseFare + 10.00 : baseFare;

            fareInput.value = finalFare.toFixed(2);
        });

        document.addEventListener("DOMContentLoaded", function() {
            const numberInput = document.getElementById('studentId');

            numberInput.addEventListener("input", function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>