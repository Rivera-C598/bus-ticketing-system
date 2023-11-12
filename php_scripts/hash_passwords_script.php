<?php
include 'password_helper.php';
include '../database_config/db_config.php';

// Select all rows
$stmt = $pdo->query("SELECT * FROM admin_users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update all admin passwords, hash admin passwords
foreach ($users as $user) {
    $userId = $user['id'];
    $plainPassword = $user['password'];

    $salt = generateUniqueSalt();

    $hashedPassword = hashPassword($plainPassword, $salt);  

    $updateStmt = $pdo->prepare("UPDATE admin_users SET password = :hashedPassword, salt = :salt WHERE id = :userId");
    $updateStmt->bindParam(':hashedPassword', $hashedPassword);
    $updateStmt->bindParam(':salt', $salt);  
    $updateStmt->bindParam(':userId', $userId);
    $updateStmt->execute();
}

echo "Passwords hashed and salted orayt";
?>
