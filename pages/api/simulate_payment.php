<?php
session_start();
require_once '../../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$booking_id = $input['booking_id'] ?? 0;

if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mã đơn hàng không hợp lệ']); exit;
}

// Lấy thông tin để gửi mail (nếu cần)
$sql = "SELECT b.id, b.final_price, c.email as customer_email FROM bookings b LEFT JOIN users c ON b.customer_id = c.id WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']); exit;
}

// --- CẬP NHẬT MẠNH TAY: Chuyển thẳng sang COMPLETED để Admin tính tiền ---
$update = $conn->prepare("UPDATE bookings SET payment_status = 'paid', status = 'completed' WHERE id = ?");
$update->bind_param("i", $booking_id);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Thanh toán thành công! Đơn hàng đã hoàn tất.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật Database']);
}
?>