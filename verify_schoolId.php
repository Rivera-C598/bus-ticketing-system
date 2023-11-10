<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $schoolId = $_POST['schoolId'];

    $result = checkForSchoolIdAndLastRequestStatus($schoolId);

    echo json_encode($result);
}

function checkForSchoolIdAndLastRequestStatus($schoolId)
{
    if (!is_string($schoolId) || empty($schoolId)) {
        return ['schoolIdExists' => false, 'lastRequestStatus' => false];
    } else {
        include 'db_config.php';

        $query = "SELECT COUNT(*) FROM student_reference WHERE student_id = :schoolId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':schoolId', $schoolId, PDO::PARAM_STR);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        // set timezone to Asia/Manila 
        date_default_timezone_set('Asia/Manila');

        if ($count > 0) {
            $cooldownQuery = "SELECT MAX(request_timestamp) AS last_request FROM ticket_requests WHERE student_id = :studentId";
            $cooldownStmt = $pdo->prepare($cooldownQuery);
            $cooldownStmt->bindParam(':studentId', $schoolId);
            $cooldownStmt->execute();
            $lastRequest = $cooldownStmt->fetch(PDO::FETCH_ASSOC)['last_request'];

            if (!$lastRequest) {
                return ['schoolIdExists' => true, 'lastRequestStatus' => false, 'passedCooldownPeriod' => true];
            } else {
                $currentTimestamp = time();
                $lastRequestTimestamp = strtotime($lastRequest);
                $timeElapsed = $currentTimestamp - $lastRequestTimestamp;

                //5 min cooldown
                $cooldownPeriod = 5 * 60;

                if ($timeElapsed >= $cooldownPeriod) {
                    return ['schoolIdExists' => true, 'lastRequestStatus' => true, 'passedCooldownPeriod' => true];
                }else{
                    return ['schoolIdExists' => true, 'lastRequestStatus' => true, 'passedCooldownPeriod' => false];
                }
            }

        } else {
            return ['schoolIdExists' => false, 'lastRequestStatus' => false, 'passedCooldownPeriod' => false];
        }
    }
}
?>
