<?php
/*require 'db_config.php'; 
require 'password_helper.php';


$plateNumber = $_POST['plateNumber'];
$driverName = $_POST['driverName'];
$contactNum = $_POST['contactNum'];

$route = $_POST['route'];
$capacity = $_POST['capacity'];
$air_conditioned = isset($_POST['air_conditioned']) ? 1 : 0;

$query = "INSERT INTO buses (route, capacity, air_conditioned) VALUES (:route, :capacity, :air_conditioned)";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':route', $route);
$stmt->bindParam(':capacity', $capacity);
$stmt->bindParam(':air_conditioned', $air_conditioned);

if ($stmt->execute()) {
    //bus is good shit
    header("Location: manage_buses.php");
    exit();
} else {
    //noo
    echo "Error adding the bus.";
}*/

require '../../../../database_config/db_config.php';
include '../../../../time/time_conf.php';

if (isset($_FILES['busPhoto'])) {
    $currentDateTime = date('Y-m-d H:i:s');

    $uploadDir = '../../../../uploads/';  //folder for photos
    $defaultImage = '../../../../uploads/default_bus.png';
    $uploadFile = $uploadDir . basename($_FILES['busPhoto']['name']);

    // Check if a file was uploaded
    if (!empty($_FILES['busPhoto']['name'])) {
        //check if iz too big (5mb)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;  //5mb

        if (
            in_array($_FILES['busPhoto']['type'], $allowedTypes) &&
            $_FILES['busPhoto']['size'] <= $maxSize
        ) {
            if (move_uploaded_file($_FILES['busPhoto']['tmp_name'], $uploadFile)) {
                //yay
            } else {
                //no
                $response = array('status' => 'error adding bus photo');
                echo json_encode($response);
                exit();
            }
        } else {
            $response = array('status' => 'photo exceeded 5mb');
            echo json_encode($response);
            exit();
        }
    } else {
        //when nothing
        $uploadFile = $defaultImage;
    }

    //sanitize inputs
    $plateNumber = filter_input(INPUT_POST, 'plateNumber', FILTER_SANITIZE_SPECIAL_CHARS);
    $driverName = filter_input(INPUT_POST, 'driverName', FILTER_SANITIZE_SPECIAL_CHARS);
    $contactNum = filter_input(INPUT_POST, 'contactNum', FILTER_SANITIZE_SPECIAL_CHARS);
    $route = filter_input(INPUT_POST, 'route', FILTER_SANITIZE_SPECIAL_CHARS);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $air_conditioned = isset($_POST['air_conditioned']) ? 1 : 0;

    $contactNum = "+63" . $contactNum;

    $createdAt = $currentDateTime;
    $updatedAt = $currentDateTime;

    if (!isValidPhoneNumber($contactNum)) {
        $response = array('status' => 'invalid phone number');
        echo json_encode($response);
        exit();
    }

    if ($capacity <= 0) {
        $response = array('status' => 'invalid capacity');
        echo json_encode($response);
        exit();
    }

    //default status of a newly added bus
    $status = "unavailable";

    //insert data
    $query = "INSERT INTO buses (plate_number, bus_driver_name, driver_contact_num, route, capacity, air_conditioned, busPhoto, created_at, updated_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$plateNumber, $driverName, $contactNum, $route, $capacity, $air_conditioned, $uploadFile, $createdAt, $updatedAt, $status])) {
        $response = array('status' => 'success');
        echo json_encode($response);
        exit();
    } else {
        $response = array('status' => 'error', 'message' => 'Error adding the bus.');
        echo json_encode($response);
        exit();
    }
}
function isValidPhoneNumber($phoneNumber)
{
    $pattern = '/^\+63\d{10}$/';

    return preg_match($pattern, $phoneNumber);
}
