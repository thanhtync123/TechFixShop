<?php
session_start();
include '../../config/db.php';
require_once '../../libs/send_mail.php';

if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'admin') {
    die("Bạn không có quyền.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
    $technician_id = isset($_POST['technician_id']) ? (int) $_POST['technician_id'] : 0;

    if ($booking_id && $technician_id) {

        $stmt = $conn->prepare("UPDATE bookings SET technician_id = ?, status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("ii", $technician_id, $booking_id);
        $stmt->execute();
        $stmt->close();

        $sqlInfo = "
            SELECT b.customer_id, b.appointment_time, u.email, u.name AS customer_name,
                   t.name AS tech_name, t.phone AS tech_phone
            FROM bookings b
            JOIN users u ON b.customer_id = u.id
            JOIN users t ON b.technician_id = t.id
            WHERE b.id = ?
        ";
        $stmtInfo = $conn->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $booking_id);
        $stmtInfo->execute();
        $customer = $stmtInfo->get_result()->fetch_assoc();
        $stmtInfo->close();

        if ($customer && !empty($customer['email'])) {
            $customer_email = $customer['email'];
            
            $mailData = [
                'customer_name' => $customer['customer_name'] ?? 'Khách hàng',
                'booking_id'    => $booking_id,
                'technician'    => $customer['tech_name'],
                'tech_phone'    => $customer['tech_phone'],
                'appointment'   => date("H:i d/m/Y", strtotime($customer['appointment_time']))
            ];

            @sendBookingEmail($customer_email, $mailData, 'assigned');
            
            $message_chuong = "Đơn hàng #{$booking_id} đã được kỹ thuật viên {$customer['tech_name']} tiếp nhận!";
            $stmtNotify = $conn->prepare("INSERT INTO notifications (customer_id, message) VALUES (?, ?)");
            $cid = (int)$customer['customer_id'];
            $stmtNotify->bind_param("is", $cid, $message_chuong);
            $stmtNotify->execute();
            $stmtNotify->close();
        }
    }

    header("Location: admin_dispatch.php");
    exit;
}
?>