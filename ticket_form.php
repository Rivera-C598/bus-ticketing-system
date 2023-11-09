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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $busId = $_POST['busId'];

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


                //form for processing tickets -->
                echo '<form class="ticket-processing-form" action="process_tickets.php" method="post">';
                echo '<h3 class="text-center">Book Tickets</h3>';
                echo '<label for="studentId">Student ID:</label>';
                echo '<input type="text" id="studentId" name="studentId" required maxlength="7">';
                echo '<label for="busStop">Your Stop:</label>';
                echo '<select id="busStop" name="busStop" required>';
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
                echo '<input type="hidden" id="busId" name="busId" value="' . $busId . '">';
            }
        }
        ?>
        <label for="fare">Fare:</label>
        <div class="input-group flex-nowrap">
            <div class="input-group-prepend">
                <span class="input-group-text" id="addon-wrapping">Php</span>
            </div>
            <input type="text" id="fare" name="fare" value="10.00" readonly>
        </div>

        <button type="submit" class="btn btn-primary center-button">Confirm & submit</button>

        </form>

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

        setInterval(updateAvailableSlots, 3000); //3 sekuz update
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
            if (fares.hasOwnProperty(selectedOption)) {
                fareInput.value = fares[selectedOption].toFixed(2);
            } else {
                fareInput.value = '10.00'; //default value
            }
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