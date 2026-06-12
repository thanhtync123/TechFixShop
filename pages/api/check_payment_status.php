<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception("Thiếu ID đơn hàng");
    }

    $id = (int)$_GET['id'];
    if ($id <= 0) {
        throw new Exception("ID không hợp lệ");
    }

    $stmt = $conn->prepare("
        SELECT payment_status
        FROM bookings
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        echo json_encode([
            'success' => true,
            'status' => $data['payment_status']   
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'status' => 'not_found'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
