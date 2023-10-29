<?php
require 'db_config.php'; 
require 'password_helper.php'; 

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['verification_code'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $verificationCode = $_POST['verification_code'];

    // Check if the username already exists
    $checkQuery = "SELECT COUNT(*) FROM admin_users WHERE username = :username";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        echo "Username already exists. Please choose a different username.";
    } else {
        $theVerificationCode = "696969";

        if ($theVerificationCode === $verificationCode) {
            $salt = generateUniqueSalt();
            $hashedPassword = hashPassword($password, $salt);

            $insertQuery = "INSERT INTO admin_users (username, password, salt) VALUES (:username, :password, :salt)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':password', $hashedPassword);
            $insertStmt->bindParam(':salt', $salt);

            if ($insertStmt->execute()) {
                echo "Admin registration successful.";
                //header("Location: admin_control_panel.php");
            } else {
                echo "Error registering admin.";
            }
        } else {
            echo "Verification code does not match.";
        }
    }
}

?>
