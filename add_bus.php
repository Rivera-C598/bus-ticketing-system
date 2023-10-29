<?php
require 'db_config.php'; // Include your database configuration file
require 'password_helper.php'; // Include any helper files you need

$bus_id = $_POST['bus_id'];
$route = $_POST['route'];
$fare = $_POST['fare'];
$capacity = $_POST['capacity'];
$air_conditioned = isset($_POST['air_conditioned']) ? 1 : 0;

$query = "INSERT INTO buses (bus_id, route, fare, capacity, air_conditioned) VALUES (:bus_id, :route, :fare, :capacity, :air_conditioned)";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':bus_id', $bus_id);
$stmt->bindParam(':route', $route);
$stmt->bindParam(':fare', $fare);
$stmt->bindParam(':capacity', $capacity);
$stmt->bindParam(':air_conditioned', $air_conditioned);

if ($stmt->execute()) {
    // Bus successfully added
    header("Location: manage_buses.php"); // Redirect back to the manage buses page or wherever you want
    exit();
} else {
    // An error occurred during insertion
    echo "Error adding the bus.";
}



?>