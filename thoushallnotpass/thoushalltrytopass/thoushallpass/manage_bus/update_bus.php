<?php
include '../../../../database_config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busId = $_POST['busId'];
    $plateNumber = $_POST['editPlateNumber'];
    $driverName = $_POST['editDriverName'];
    $driverContactNum = $_POST['editDriverContactNum'];
    $route = $_POST['editRoute'];
    $capacity = $_POST['editCapacity'];
    $airConditioned = isset($_POST['editAirConditioned']) ? 1 : 0;

    //check if a new photo has been uploaded
    if (isset($_FILES['newBusPhoto']) && $_FILES['newBusPhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadsDirectory = '../../../../uploads/';
        $targetFile = $uploadsDirectory . basename($_FILES['newBusPhoto']['name']);

        //check if the uploaded file is an image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($imageFileType, $allowedExtensions)) {
            //photo upload
            if (move_uploaded_file($_FILES['newBusPhoto']['tmp_name'], $targetFile)) {

                $busPhoto = $targetFile;
            } else {

                $busPhoto = $_POST['currentBusPhoto'];
            }
        } else {
            $busPhoto = $_POST['currentBusPhoto'];
        }
    } else {
        $busPhoto = $_POST['currentBusPhoto'];
    }

    $driverContactNum = "+63" . $driverContactNum;

    if (!isValidPhoneNumber($driverContactNum)) {
        $response = array('status' => 'invalid phone number');
        echo json_encode($response);
        exit();
    }

    if ($capacity <= 0) {
        $response = array('status' => 'invalid capacity');
        echo json_encode($response);
        exit();
    }



    $query = "UPDATE buses SET plate_number = :plateNumber, bus_driver_name = :driverName, driver_contact_num = :driverContactNum, route = :route, capacity = :capacity, air_conditioned = :airConditioned, busPhoto = :busPhoto
          WHERE bus_id = :busId";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':plateNumber', $plateNumber);
    $stmt->bindValue(':driverName', $driverName);
    $stmt->bindValue(':driverContactNum', $driverContactNum);
    $stmt->bindValue(':route', $route);
    $stmt->bindValue(':capacity', $capacity);
    $stmt->bindValue(':airConditioned', $airConditioned);
    $stmt->bindValue(':busPhoto', $busPhoto);
    $stmt->bindValue(':busId', $busId);

    if ($stmt->execute()) {

        $response = array('status' => 'success');
    } else {
        $response = array('status' => 'error');
    }
    echo json_encode($response);
}
function isValidPhoneNumber($phoneNumber)
{
    $pattern = '/^\+63\d{10}$/';

    return preg_match($pattern, $phoneNumber);
}
