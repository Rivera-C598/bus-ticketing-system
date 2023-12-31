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
                $query = $conn->prepare('SELECT id, full_name FROM admin_users WHERE username = :enteredUsername');
                $query->bindParam(':enteredUsername', $enteredUsername);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    session_start();
                    $_SESSION['admin_id'] = $result['id'];
                    $_SESSION['admin_name'] = $result['full_name'];
                    $_SESSION['authenticated'] = true;
                    header('Location: thoushallpass/mirage/admin_control_panel.php');
                    exit();
                }
            } else {
                header('Location: ../../info/error_page.php?error=invalid_credentials');
            }
        } else {
            echo ("Admin not found.");
        }
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}

//nooo 
header('Location: ../../info/error_page.php?error=invalid_credentials');
exit();
