<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f0f0f0;
            padding: 20px;
        }

        .wrapper {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
        }

        .btn {
            margin: 5px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <title>Bus Ticketing System - Route Selection Page</title>
</head>
<body>
    <div class="wrapper">
        <label>North</label>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('North')">Bogo City</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('North')">Carmen</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('North')">Sogod</button>

        <label>South</label>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Compostela</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Liloan</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Lacion</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Consolacion</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Mandaue</button>
        <button class="btn btn-primary" onclick="redirectToRouteDetail('South')">Cebu</button>
    </div>

    <script>
    function redirectToRouteDetail(route) {
        window.location.href = 'route_detail.php?route=' + route;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
