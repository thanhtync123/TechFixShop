<?php
header('Content-Type: text/html; charset=utf-8');
// Tắt lệnh ẩn lỗi để xem nhật ký kết nối
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mailConfig = [
    'host'       => 'smtp.gmail.com',
    'port'       => 465,
    'secure'     => 'ssl',
    'username'   => '22004073@st.vlute.edu.vn', 
    'password'   => 'dehf ycwy urqg wzmi',       
    'from_email' => '22004073@st.vlute.edu.vn',
    'from_name'  => 'TEST TECHFIX'
];

// Tự động tìm thư mục thư viện
$manualPath = __DIR__ . '/../libs/PHPMailer';
if (is_dir($manualPath)) {
    require_once $manualPath . "/src/Exception.php";
    require_once $manualPath . "/src/PHPMailer.php";
    require_once $manualPath . "/src/SMTP.php";
}

if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
    die("LỖI: Không tìm thấy thư viện PHPMailer.");
}

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->SMTPDebug = 2; // Bật nhật ký chi tiết mã lỗi của Google
    $mail->isSMTP();
    $mail->Host       = $mailConfig['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailConfig['username'];
    $mail->Password   = $mailConfig['password'];
    $mail->Port       = $mailConfig['port'];
    $mail->SMTPSecure = $mailConfig['secure'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
    $mail->addAddress($mailConfig['username'], 'Test User'); // Tự gửi cho chính mình

    $mail->isHTML(true);
    $mail->Subject = "Kiểm tra kết nối SMTP";
    $mail->Body    = "Nếu thấy dòng này thì hệ thống đã chạy thành công!";

    echo "<h3>--- ĐANG KẾT NỐI ĐẾN GOOGLE SMTP ---</h3>";
    $mail->send();
    echo "<h3 style='color:green;'>GỬI MAIL THÀNH CÔNG!</h3>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>GỬI MAIL THẤT BẠI!</h3>";
    echo "<b>Mã lỗi chi tiết:</b> " . $e->getMessage();
}
?>