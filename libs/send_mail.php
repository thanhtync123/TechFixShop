<?php

// 1. TỰ ĐỘNG QUÉT TÌM THƯ MỤC THƯ VIỆN Ở TẤT CẢ CÁC VỊ TRÍ CÓ THỂ ĐẶT VÀO
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    $manualPath = '';
    // Tự động quét tìm cả trong thư mục libs và ngoài thư mục gốc dự án
    $possiblePaths = [
        __DIR__ . '/PHPMailer',
        __DIR__ . '/PHPMailer-master',
        __DIR__ . '/../PHPMailer',
        __DIR__ . '/../PHPMailer-master'
    ];

    foreach ($possiblePaths as $path) {
        if (is_dir($path)) {
            $manualPath = $path;
            break;
        }
    }
    
    if (!empty($manualPath)) {
        $separator = DIRECTORY_SEPARATOR;
        require_once $manualPath . "{$separator}src{$separator}Exception.php";
        require_once $manualPath . "{$separator}src{$separator}PHPMailer.php";
        require_once $manualPath . "{$separator}src{$separator}SMTP.php";
    }
}


function sendBookingEmail($toEmail, $data, $type = 'new') {
    static $mailConfig = null;

    if ($mailConfig === null) {
        $defaultConfig = [
            'host'       => 'smtp.gmail.com',
            'port'       => 465,
            'secure'     => 'ssl',
            'username'   => '22004073@st.vlute.edu.vn',
            'password'   => 'dehf ycwy urqg wzmi', 
            'from_email' => '22004073@st.vlute.edu.vn',
            'from_name'  => 'TECHFIX Support',
        ];
        
        $configPath = __DIR__ . '/../config/mail.php';
        if (file_exists($configPath)) {
            $loadedConfig = require $configPath;
            $mailConfig = is_array($loadedConfig) ? array_merge($defaultConfig, $loadedConfig) : $defaultConfig;
        } else {
            $mailConfig = $defaultConfig;
        }
    }

    $phpMailerClass = '\\PHPMailer\\PHPMailer\\PHPMailer';
    $canUsePHPMailer = class_exists($phpMailerClass);

    try {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ.');
        }

        if (!$canUsePHPMailer) {
            throw new Exception('Không thể tìm thấy thư viện PHPMailer ở các thư mục cấu hình.');
        }

        $customerName = htmlspecialchars($data['customer_name'] ?? 'Quý khách');
        $bookingId    = $data['booking_id'] ?? '...';
        
        $techName     = htmlspecialchars($data['technician'] ?? '');
        $techPhone    = htmlspecialchars($data['tech_phone'] ?? '');
        $appointment  = htmlspecialchars($data['appointment'] ?? '');

        $brandColor = '#0056b3'; 
        $bgColor    = '#f4f6f8'; 

        $emailHeader = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { margin: 0; padding: 0; background-color: $bgColor; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
                .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
                .header { background-color: $brandColor; padding: 30px 20px; text-align: center; }
                .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; }
                .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
                .info-box { background-color: #f8f9fa; border-left: 4px solid $brandColor; padding: 15px; margin: 20px 0; border-radius: 4px; }
                .btn { display: inline-block; padding: 12px 25px; background-color: $brandColor; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
                .footer { background-color: #e9ecef; padding: 20px; text-align: center; font-size: 12px; color: #6c757d; }
            </style>
        </head>
        <body>
            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: $bgColor; padding: 20px;'>
                <tr>
                    <td align='center'>
                        <div class='container'>
                            <div class='header'>
                                <h1>TECHFIX SERVICE</h1>
                                <div style='color: rgba(255,255,255,0.8); font-size: 14px; margin-top: 5px;'>Sửa đúng lỗi - Báo đúng giá</div>
                            </div>
        ";

        $emailFooter = "
                            <div class='footer'>
                                <p>Bạn nhận được email này vì đã sử dụng dịch vụ tại <b>TECHFIX</b>.</p>
                                <p>Địa chỉ: 73 Nguyễn Huệ, TP. Vĩnh Long | Hotline: 1900 1234</p>
                                <p>&copy; " . date('Y') . " TechFix Inc. All rights reserved.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $bodies = [
            'new' => "
                <div class='content'>
                    <h2 style='color: #333; margin-top: 0;'>🎉 Đặt lịch thành công!</h2>
                    <p>Xin chào <strong>$customerName</strong>,</p>
                    <p>Cảm ơn bạn đã tin tưởng dịch vụ của TechFix. Đơn đặt lịch của bạn đã được ghi nhận thành công.</p>
                    <div class='info-box'>
                        <table width='100%'>
                            <tr>
                                <td style='padding: 5px 0; color: #666;'>Mã đơn hàng:</td>
                                <td style='text-align: right; font-weight: bold;'>#$bookingId</td>
                            </tr>
                            <tr>
                                <td style='padding: 5px 0; color: #666;'>Trạng thái:</td>
                                <td style='text-align: right; color: #ff9800; font-weight: bold;'>Đang chờ xác nhận</td>
                            </tr>
                        </table>
                    </div>
                    <p>Chúng tôi sẽ sớm liên hệ để xác nhận tình trạng thiết bị và điều phối kỹ thuật viên.</p>
                </div>
            ",

            'paid' => "
                <div class='content'>
                    <h2 style='text-align: center; color: #28a745; margin-top: 0;'>Thanh Toán Thành Công</h2>
                    <p>Xin chào <strong>$customerName</strong>,</p>
                    <p>TechFix xác nhận đã nhận được thanh toán cho đơn hàng <b>#$bookingId</b>.</p>
                    <div class='info-box' style='border-left-color: #28a745;'>
                        <p style='margin: 0; text-align: center; font-size: 16px;'>Cảm ơn bạn đã sử dụng dịch vụ!</p>
                    </div>
                </div>
            ",

            'assigned' => "
                <div class='content'>
                    <h2 style='color: #0056b3; margin-top: 0;'>🚀 Kỹ thuật viên đang đến!</h2>
                    <p>Xin chào <strong>$customerName</strong>,</p>
                    <p>Đơn hàng <b>#$bookingId</b> của bạn đã được tiếp nhận. Dưới đây là thông tin kỹ thuật viên sẽ hỗ trợ bạn:</p>
                    <div style='background-color: #e7f1ff; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                        <img src='https://cdn-icons-png.flaticon.com/512/4006/4006173.png' width='60' style='margin-bottom: 10px;'>
                        <h3 style='margin: 5px 0; color: #333;'>$techName</h3>
                        <p style='margin: 5px 0; font-size: 18px; font-weight: bold; color: #0056b3;'>📞 $techPhone</p>
                        <p style='margin: 5px 0; color: #666; font-size: 14px;'>Thời gian dự kiến: <b>$appointment</b></p>
                    </div>
                    <p>Vui lòng chú ý điện thoại để nhận cuộc gọi xác nhận từ kỹ thuật viên trước khi đến.</p>
                </div>
            "
        ];

        $finalHtmlBody = $emailHeader . $bodies[$type] . $emailFooter;
        $plainTextBody = strip_tags($finalHtmlBody);

        $mail = new $phpMailerClass(true);
        $mail->isSMTP();
        $mail->SMTPSecure = (strtolower($mailConfig['secure']) === 'tls') ? 'tls' : 'ssl';
        $mail->Host       = $mailConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->Port       = $mailConfig['port'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($toEmail, $customerName);

        $mail->isHTML(true);
        $subjects = [
            'new'      => "[TECHFIX] ✅ Xác nhận đơn hàng #$bookingId",
            'paid'     => "[TECHFIX] 💰 Thanh toán thành công #$bookingId",
            'assigned' => "[TECHFIX] 🛠️ Kỹ thuật viên đã nhận lịch #$bookingId"
        ];
        $mail->Subject = $subjects[$type] ?? "Thông báo từ TechFix";
        $mail->Body    = $finalHtmlBody;
        $mail->AltBody = $plainTextBody;

        $mail->send();
        return true;

    } catch (\Throwable $e) {
        error_log("Mail Error: " . $e->getMessage());
        return false;
    }
}
?>