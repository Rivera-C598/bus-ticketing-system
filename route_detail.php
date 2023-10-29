<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .wrapper {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            margin: 5px;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }

        .modal-body {
            background-color: #f4f4f4;
        }

        .modal-footer {
            border-radius: 0 0 10px 10px;
        }

        .modal-title {
            font-size: 24px;
        }
    </style>
    <title>Bus Ticketing System - Route Selection Page</title>
</head>
<body>
    <div class="wrapper">
        <h2 class="text-center mb-4">Buses on the North Route</h2>
        <div class="d-flex justify-content-center">
            <button class="btn btn-primary" data-toggle="modal" data-target="#busModal1">Bus 1</button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#busModal2">Bus 2</button>
        </div>
    </div>

    <!-- bus1 modal -->
    <!-- ywa di mogana -->
    <div class="modal fade" id="busModal1" tabindex="-1" role="dialog" aria-labelledby="busModal1Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="busModal1Label">Bus 1 Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Bus ID: 69</p>
                    <p>Route: ambot</p>
                    <p>Fare: one million</p>
                    <p>Capacity: 69</p>
                    <p>Ai</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Book</button>
                </div>
            </div>
        </div>
    </div>

    <!-- bus2 -->
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
