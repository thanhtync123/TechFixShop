<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once '../config/db.php';
require_once __DIR__ . '/../libs/pricing.php';   
require_once __DIR__ . '/../libs/send_mail.php'; 

function respond_json(bool $success, string $message, array $extra = []): void
{
    http_response_code($success ? 200 : 400);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'customer') {
    respond_json(false, 'Bạn cần đăng nhập bằng tài khoản khách hàng.');
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    respond_json(false, 'Dữ liệu gửi lên không hợp lệ.');
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
    respond_json(false, 'Lỗi phiên làm việc, vui lòng đăng nhập lại.');
}

if ($serviceId <= 0) {
    respond_json(false, 'Vui lòng chọn dịch vụ.');
}

if (empty($district) || empty($appointmentDate) || empty($appointmentSlot)) {
    respond_json(false, 'Vui lòng nhập đầy đủ khu vực, ngày và giờ hẹn.');
}


// $allowed_locations = [
//     'Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 
//     'Phường 5', 'Phường 8', 'Phường 9',
//     'Phường Trường An', 'Phường Tân Ngãi', 'Phường Tân Hòa', 'Phường Tân Hội',
//     'Xã Tân Hạnh', 'Xã Hòa Phú', 'Xã Phước Hậu', 'Xã Thanh Đức'
// ];

// if (!in_array($district, $allowed_locations)) {
//     respond_json(false, 'Xin lỗi, TechFix hiện chỉ phục vụ trong khu vực Thành phố Vĩnh Long.');
// }


$dateObj = DateTime::createFromFormat('Y-m-d', $appointmentDate);
if (!$dateObj) {
    respond_json(false, 'Ngày hẹn không hợp lệ.');
}

$slotObj = DateTime::createFromFormat('H:i:s', $appointmentSlot);
if (!$slotObj) {
    $slotObj = DateTime::createFromFormat('H:i', $appointmentSlot);
    if (!$slotObj) {
        respond_json(false, 'Khung giờ không hợp lệ.');
    }
}

$stmtService = $conn->prepare("SELECT name, price FROM services WHERE id = ? LIMIT 1");
$stmtService->bind_param('i', $serviceId);
$stmtService->execute();
$serviceResult = $stmtService->get_result();
$service = $serviceResult->fetch_assoc();
$stmtService->close();

if (!$service) {
    respond_json(false, 'Dịch vụ không tồn tại.');
}


[$finalPrice, $priceNotes] = calculateSmartQuote((float) $service['price'], $district, $appointmentDate, $appointmentSlot);

$status = 'pending';
$noteParts = ["Khung giờ {$appointmentSlot}"];
if (!empty($priceNotes)) {
    $noteParts[] = implode(' | ', $priceNotes);
}
$note = implode(' - ', $noteParts);


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


$sql = "INSERT INTO bookings (customer_id, customer_name, phone, address, district, service_id, appointment_time, note, final_price, status, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmtInsert = $conn->prepare($sql);

$appointmentDateOnly = $dateObj->format('Y-m-d');
$finalPriceValue = round($finalPrice, 0);

$stmtInsert->bind_param(
    'issssissdsdd',
    $customerId,      
    $customerName,    
    $customerPhone,   
    $address,         
    $district,        
    $serviceId,       
    $appointmentDateOnly, 
    $note,            
    $finalPriceValue, 
    $status,          
    $lat,             
    $lng              
);

if ($stmtInsert->execute()) {
    $newBookingId = $stmtInsert->insert_id;

    if (!empty($customerEmail)) {
        try {
            $mailData = [
                'customer_name' => $customerName,
                'booking_id'    => $newBookingId
            ];
            
            sendBookingEmail($customerEmail, $mailData, 'new');
            
        } catch (Exception $e) {
            error_log("Mail sending failed for ID $newBookingId: " . $e->getMessage());
        }
    }

    respond_json(true, 'Đặt lịch thành công!', [
        'booking_id'   => $newBookingId,
        'final_price'  => $finalPriceValue,
        'has_location' => ($lat && $lng) ? true : false
    ]);

} else {
    error_log("DB Insert Error: " . $stmtInsert->error);
    respond_json(false, 'Lỗi hệ thống khi lưu đơn hàng. Vui lòng thử lại sau.');
}

$stmtInsert->close();
$conn->close();
?>