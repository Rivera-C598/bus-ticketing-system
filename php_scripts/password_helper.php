<?php
function generateUniqueSalt() {
    return bin2hex(random_bytes(16));
}

function hashPassword($password, $salt) {
    return password_hash($password, PASSWORD_BCRYPT);
}


function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}


?>
