<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/ticketing_styles.css">

    <title>Bus Ticketing System - Available Buses</title>
</head>

<body>
    <?php
    include '../database_config/db_config.php';

    $route = isset($_GET['route']) ? $_GET['route'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';

    if (
        isset($_GET['route']) && !empty($_GET['route']) &&
        isset($_GET['status']) && !empty($_GET['status'])
    ) {

        $route = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($route !== null && $status !== null) {

            $query = "SELECT * FROM buses";
            $query .= " WHERE route = :route AND status = :status";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':route', $route, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            $stmt->execute();
        } else {
            echo "Invalid input parameters.";
        }
    } else {
        echo "One or more parameters are missing or empty.";
    }

    echo "<div class='wrapper'>";
    echo "<h1 class='display-6 text-center'>Available buses " . $route . "</h1>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $busId = $row['bus_id'];
        $plateNumber = $row['plate_number'];

        //oytput some sort of link or any representation for each bus
        echo "<a href='rev1/rev2/rev3/ticket_form.php?busId=" . $busId . "' class='btn btn-primary'>" . $plateNumber . "</a>";

    }

    echo "</div>";
    ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>