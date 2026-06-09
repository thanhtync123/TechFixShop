<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/db.php'; 

if (!isset($_SESSION['user'])) {
    echo json_encode(['has_new' => false]);
    exit;
}

$user_id = $_SESSION['user']['id'];


$col_name = 'user_id'; 

$sql = "SELECT * FROM notifications WHERE $col_name = ? AND status = 'unread' ORDER BY created_at ASC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notif = $result->fetch_assoc();

if ($notif) {
    $update = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
    $update->bind_param("i", $notif['id']);
    $update->execute();

    echo json_encode([
        'has_new' => true,
        'message' => $notif['message'],
        'time' => date('H:i', strtotime($notif['created_at']))
    ]);
} else {
    echo json_encode(['has_new' => false]);
}
?>