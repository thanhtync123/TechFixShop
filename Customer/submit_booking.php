<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

// Kích hoạt chế độ bắt mọi lỗi ngầm của MySQLi thành Exception để try-catch xử lý
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond_json(bool $success, string $message, array $extra = []): void
{
    // Giữ response code 200 để chuỗi JSON luôn được truyền tải về giao diện không bị chặn mạng
    http_response_code(200);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 1. KIỂM TRA VÀ NẠP CÁC FILE HỆ THỐNG
    if (!file_exists('../config/db.php')) {
        throw new Exception('Không tìm thấy file kết nối CSDL tại đường dẫn ../config/db.php');
    }
    require_once '../config/db.php';

    if (!file_exists(__DIR__ . '/../libs/pricing.php')) {
        throw new Exception('Không tìm thấy file tính toán giá tại đường dẫn libs/pricing.php');
    }
    require_once __DIR__ . '/../libs/pricing.php';   

    if (!file_exists(__DIR__ . '/../libs/send_mail.php')) {
        throw new Exception('Không tìm thấy thư viện gửi mail tại đường dẫn libs/send_mail.php');
    }
    require_once __DIR__ . '/../libs/send_mail.php'; 

    // 2. KIỂM TRA PHIÊN ĐĂNG NHẬP
    if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'customer') {
        respond_json(false, 'Phiên làm việc đã hết hạn, vui lòng đăng nhập lại.');
    }

    // 3. ĐỌC DỮ LIỆU RAW JSON TỪ TRANG BOOK.PHP TRUYỀN SANG
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        respond_json(false, 'Dữ liệu đơn đặt lịch gửi lên không hợp lệ.');
    }

    $customer      = $_SESSION['user'];
    $customerId    = (int) ($customer['id'] ?? 0);
    $customerName  = $customer['name'] ?? 'Khách hàng';
    $customerPhone = $customer['phone'] ?? '';
    $customerEmail = $customer['email'] ?? ''; 
    $defaultAddress = $customer['address'] ?? '';

    $serviceId        = isset($payload['ServiceId']) ? (int) $payload['ServiceId'] : 0;
    $district         = trim($payload['District'] ?? '');
    $appointmentDate  = $payload['AppointmentDate'] ?? '';
    $appointmentSlot  = $payload['AppointmentTime'] ?? '';
    $address          = trim($payload['Address'] ?? $defaultAddress);

    if ($customerId <= 0) {
        respond_json(false, 'Mã khách hàng không hợp lệ, vui lòng đăng nhập lại.');
    }
    if ($serviceId <= 0) {
        respond_json(false, 'Vui lòng chọn một dịch vụ sửa chữa.');
    }
    if (empty($district) || empty($appointmentDate) || empty($appointmentSlot)) {
        respond_json(false, 'Vui lòng điền đầy đủ khu vực, ngày và giờ hẹn.');
    }

    // 4. KIỂM TRA ĐỊNH DẠNG THỜI GIAN
    $dateObj = DateTime::createFromFormat('Y-m-d', $appointmentDate);
    if (!$dateObj) {
        respond_json(false, 'Định dạng ngày hẹn không hợp lệ.');
    }

    $slotObj = DateTime::createFromFormat('H:i:s', $appointmentSlot);
    if (!$slotObj) {
        $slotObj = DateTime::createFromFormat('H:i', $appointmentSlot);
        if (!$slotObj) {
            respond_json(false, 'Định dạng khung giờ hẹn không hợp lệ.');
        }
    }

    // 5. LẤY GIÁ GỐC CỦA DỊCH VỤ
    $stmtService = $conn->prepare("SELECT name, price FROM services WHERE id = ? LIMIT 1");
    $stmtService->bind_param('i', $serviceId);
    $stmtService->execute();
    $serviceResult = $stmtService->get_result();
    $service = $serviceResult->fetch_assoc();
    $stmtService->close();

    if (!$service) {
        respond_json(false, 'Dịch vụ bạn chọn không tồn tại trên hệ thống.');
    }

    // 6. TÍNH TOÁN GIÁ THÔNG MINH
    if (!function_exists('calculateSmartQuote')) {
        throw new Exception('Hàm calculateSmartQuote không tồn tại hoặc bị lỗi trong file libs/pricing.php');
    }
    [$finalPrice, $priceNotes] = calculateSmartQuote((float) $service['price'], $district, $appointmentDate, $appointmentSlot);

    $status = 'pending';
    $noteParts = ["Khung giờ {$appointmentSlot}"];
    if (!empty($priceNotes)) {
        $noteParts[] = implode(' | ', $priceNotes);
    }
    $note = implode(' - ', $noteParts);

    // 7. XỬ LÝ TỌA ĐỘ BẢN ĐỒ VỆ TINH
    $lat = null;
    $lng = null;
    try {
        $fullAddressToCheck = $address . ', ' . $district; 
        if (function_exists('getCoordinates')) {
            $coords = getCoordinates($fullAddressToCheck);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }
    } catch (Exception $e) {
        error_log("Geo Error: " . $e->getMessage());
    }

    // Gộp Ngày + Giờ thành chuỗi DATETIME đầy đủ để trang Admin không bị lỗi hiển thị về 00:00
    $appointmentFullDateTime = $dateObj->format('Y-m-d') . ' ' . $slotObj->format('H:i:s');
    $finalPriceValue = round($finalPrice, 0);

    // 8. CHUẨN BỊ LỆNH INSERT VÀO CSDL
    $sql = "INSERT INTO bookings (customer_id, customer_name, phone, address, district, service_id, appointment_time, note, final_price, status, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sql);

    $stmtInsert->bind_param(
        'issssissdsdd',
        $customerId,      
        $customerName,    
        $customerPhone,   
        $address,         
        $district,        
        $serviceId,       
        $appointmentFullDateTime, 
        $note,            
        $finalPriceValue, 
        $status,          
        $lat,             
        $lng              
    );

    if ($stmtInsert->execute()) {
        $newBookingId = $stmtInsert->insert_id;
        $stmtInsert->close();

        // 9. TIẾN HÀNH GỬI THƯ XÁC NHẬN TỰ ĐỘNG
        if (!empty($customerEmail)) {
            try {
                $mailData = [
                    'customer_name' => $customerName,
                    'booking_id'    => $newBookingId,
                    'appointment'   => date("H:i d/m/Y", strtotime($appointmentFullDateTime))
                ];
                sendBookingEmail($customerEmail, $mailData, 'new');
            } catch (Exception $e) {
                error_log("Gửi mail thất bại cho đơn hàng #$newBookingId: " . $e->getMessage());
            }
        }

        // Trả về JSON kết quả thành công mỹ mãn
        respond_json(true, 'Đặt lịch sửa chữa thành công!', [
            'booking_id'   => $newBookingId,
            'final_price'  => $finalPriceValue,
            'has_location' => ($lat && $lng) ? true : false
        ]);

    } else {
        throw new Exception("Không thể ghi dữ liệu đơn hàng vào hệ thống.");
    }

} catch (\Throwable $e) {
    // Nếu backend phát sinh bất kỳ lỗi Fatal nào, bọc lại dạng JSON trả về giao diện cho bạn nhìn thấy
    respond_json(false, 'LỖI HỆ THỐNG BACKEND: ' . $e->getMessage());
}