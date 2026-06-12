<?php
session_start();
include '../config/db.php'; 

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    echo json_encode([]); 
    exit();
}

$customer_id = $_SESSION['user']['id'] ?? 0;

if ($customer_id === 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT message, status, created_at 
    FROM notifications 
    WHERE customer_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);


$stmtUpdate = $conn->prepare("UPDATE notifications SET status = 'read' WHERE customer_id = ? AND status = 'unread'");
$stmtUpdate->bind_param("i", $customer_id);
$stmtUpdate->execute();
$stmtUpdate->close();

echo json_encode($notifications);
?>