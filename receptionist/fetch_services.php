<?php
include_once("../public/includes/db_connect.php");
header('Content-Type: application/json');

$appointment_id = intval($_GET['appointment_id'] ?? 0);
$services = [];

if ($appointment_id > 0) {
    $result = $conn->query("SELECT services_selected 
                            FROM appointments 
                            WHERE id=$appointment_id 
                              AND (status='in_progress' OR status='completed')
                            LIMIT 1");

    if ($row = $result->fetch_assoc()) {
        $services_selected = json_decode($row['services_selected'], true);

        if (!empty($services_selected)) {
            foreach ($services_selected as $s) {
                $sObj = is_string($s) ? json_decode($s, true) : $s;
                if ($sObj && isset($sObj['name']) && isset($sObj['price'])) {
                    $services[] = [
                        "name" => $sObj['name'],
                        "price" => floatval($sObj['price'])
                    ];
                }
            }
        }
    }
}

echo json_encode($services);