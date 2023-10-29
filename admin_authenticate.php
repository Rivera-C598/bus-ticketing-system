<?php
require_once 'db_config.php';
require 'password_helper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredUsername = $_POST['username'];
    $enteredPassword = $_POST['password'];

    try {
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare('SELECT password, salt FROM admin_users WHERE username = :username');
        $stmt->bindParam(':username', $enteredUsername);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $storedPassword = $row['password'];
            $salt = $row['salt'];
    
            if (verifyPassword($enteredPassword, $storedPassword, $salt)) {
                // Password is correct, admin login successful
                // Set a secure session and redirect to the admin control panel
                session_start();
                $_SESSION['authenticated'] = true;
                header('Location: admin_control_panel.php');
                exit();
            } else {
                echo("Password verification failed.");
            }
        } else {
            echo("Admin not found.");
        }
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}

// Admin login failed, 
echo("Login failed");
exit();

?>
