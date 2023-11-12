<?php
require_once '../../database_config/db_config.php';
require '../../php_scripts/password_helper.php';

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
                //password goods, admin login successful
                //start a session, only authenticated admins can cum
                session_start();
                $_SESSION['authenticated'] = true;
                header('Location: thoushallpass/mirage/admin_control_panel.php');
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

//nooo 
echo("Login failed");
exit();

?>
