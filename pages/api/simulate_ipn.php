<?php
header('Content-Type: application/json');

try {
    
    $dbPath = '../../config/db.php'; 
    
    if (!file_exists($dbPath)) {
        throw new Exception("Lỗi đường dẫn: Không tìm thấy file config/db.php tại " . realpath($dbPath));
    }
    require_once $dbPath;

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception("ID đơn hàng không hợp lệ.");
    }

    // --- UPDATED QUERY HERE ---
    // Update both status and payment_status to ensure consistency
    // If you don't have a 'payment_status' column, remove ", payment_status = 'paid'"
    $stmt = $conn->prepare("UPDATE bookings SET status = 'paid', payment_status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Lỗi SQL: " . $stmt->error);
    }

    $mailStatus = "Skipped";
    try {
        $mailLib = '../../libs/send_mail.php';
        
        if (file_exists($mailLib)) {
            require_once $mailLib;
            
            $query = $conn->query("SELECT b.customer_name, u.email 
                                   FROM bookings b 
                                   JOIN users u ON b.customer_id = u.id 
                                   WHERE b.id = $id");
            $data = $query->fetch_assoc();
            
            if ($data && !empty($data['email'])) {
                $mailData = [
                    'customer_name' => $data['customer_name'],
                    'booking_id'    => $id
                ];
                sendBookingEmail($data['email'], $mailData, 'paid');
                $mailStatus = "Sent";
            }
        }
    } catch (Exception $e) {
        error_log("Mail Error in Demo: " . $e->getMessage());
        $mailStatus = "Failed: " . $e->getMessage();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Giả lập thành công!',
        'mail_debug' => $mailStatus
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>