<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $schoolId = $_POST['schoolId'];

    $schoolIdExists = checkForSchoolId($schoolId);

    echo json_encode(['exists' => $schoolIdExists]);
}

function checkForSchoolId($schoolId)
{

    if (!is_string($schoolId) || empty($schoolId)) {
        return false;
    } else {
        include 'db_config.php';

        $query = "SELECT COUNT(*) FROM student_reference WHERE student_id = :schoolId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':schoolId', $schoolId, PDO::PARAM_STR);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        

        if($count > 0){
            return true;
        }else {
            return false;
        }
    }
}
