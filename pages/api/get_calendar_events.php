<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}
header('Content-Type: application/json');
require_once '../../config/db.php';

$query = "
    SELECT 
        b.id, b.appointment_time, b.status, b.customer_name, s.name AS service_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    WHERE b.status != 'cancelled'
";

$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        $color = '#6c757d'; 
        if ($row['status'] == 'confirmed') $color = '#0d6efd'; 
        if ($row['status'] == 'completed') $color = '#198754'; 
        if ($row['status'] == 'pending')   $color = '#ffc107'; 

        $events[] = [
            'id' => $row['id'],
            'title' => "#" . $row['id'] . " - " . $row['service_name'],
            'start' => $row['appointment_time'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'status' => $row['status'],
                'customer' => $row['customer_name']
            ]
        ];
    }
}

echo json_encode($events);
?>