<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$qr_code = $input['qr_code'] ?? '';

if (strpos($qr_code, 'TECHFIX_ORDER_') === 0) {
    $booking_id = str_replace('TECHFIX_ORDER_', '', $qr_code);
    $booking_id = intval($booking_id);

   
    $check = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $result = $check->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        if ($order['status'] == 'confirmed') {
        
            $update = $conn->prepare("UPDATE bookings SET status = 'in_progress', updated_at = NOW() WHERE id = ?");
            $update->bind_param("i", $booking_id);
            
            if ($update->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => "Đã xác nhận đến nhà khách hàng (Đơn #$booking_id). Bắt đầu tính giờ làm việc!"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật DB']);
            }
        } elseif ($order['status'] == 'in_progress') {
            echo json_encode(['success' => false, 'message' => 'Đơn này đã Check-in rồi!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Trạng thái đơn không hợp lệ (' . $order['status'] . ')']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng này']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Mã QR không đúng định dạng của TechFix']);
}
?>