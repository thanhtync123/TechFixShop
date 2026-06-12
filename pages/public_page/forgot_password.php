<?php
session_start();
require_once '../../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh'); 

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(32));
        $token_hash = hash("sha256", $token);
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30);
        
        $sql = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $token_hash, $expiry, $user['id']);

        if ($stmt->execute()) {
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/TechFixPHP/pages/public_page/reset_password.php?token=" . $token;
            $subject = "Yêu cầu khôi phục mật khẩu - TechFix";
            
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: TechFix Support <no-reply@techfix.com>" . "\r\n";
            
            $message = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    .email-container { font-family: "Helvetica Neue", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
                    .header { background: linear-gradient(135deg, #0d6efd, #0043a8); padding: 20px; text-align: center; color: white; }
                    .content { padding: 30px; color: #333333; line-height: 1.6; }
                    .btn-reset { display: inline-block; background-color: #0d6efd; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
                    .footer { background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 12px; color: #888; }
                </style>
            </head>
            <body style="background-color: #f4f4f4; padding: 20px;">
                <div class="email-container">
                    <div class="header">
                        <h1 style="margin:0; font-size: 24px;">TECHFIX SECURITY</h1>
                    </div>
                    <div class="content">
                        <p>Xin chào <strong>' . htmlspecialchars($user['name']) . '</strong>,</p>
                        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản TechFix của bạn.</p>
                        <p>Vui lòng nhấn vào nút bên dưới để thiết lập mật khẩu mới:</p>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="' . $resetLink . '" class="btn-reset">ĐẶT LẠI MẬT KHẨU</a>
                        </div>
                        
                        <p style="font-size: 13px; color: #666;"><em>* Liên kết này chỉ có hiệu lực trong vòng 30 phút.</em></p>
                        <p>Nếu bạn không yêu cầu thay đổi, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn.</p>
                    </div>
                    <div class="footer">
                        &copy; ' . date("Y") . ' TechFix Inc. All rights reserved.<br>
                        Đây là email tự động, vui lòng không trả lời.
                    </div>
                </div>
            </body>
            </html>';

            if (@mail($email, $subject, $message, $headers)) {
                $msg = "Chúng tôi đã gửi một liên kết khôi phục chuyên nghiệp đến email của bạn.";
                $msg_type = "success";
            } else {
                $msg = "Localhost Debug (Chưa có SMTP): <a href='$resetLink' class='fw-bold text-primary text-decoration-underline'>Bấm vào đây để Reset</a>";
                $msg_type = "info";
            }
        }
    } else {
        $msg = "Chúng tôi không tìm thấy tài khoản nào với email này.";
        $msg_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - TechFix</title>
    
    <link href="/TechFixPHP/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body, html { height: 100%; font-family: 'Poppins', sans-serif; background-color: #fff; }
        .row-full-height { height: 100vh; margin: 0; }
        .brand-side { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 50px; position: relative; overflow: hidden; }
        .brand-side::before { content: ''; position: absolute; top: -10%; left: -10%; width: 40%; height: 40%; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .brand-side::after { content: ''; position: absolute; bottom: -10%; right: -10%; width: 30%; height: 30%; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .brand-content { position: relative; z-index: 2; text-align: center; }
        .logo-img { width: 100px; margin-bottom: 25px; background: white; padding: 10px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .brand-title { font-weight: 800; font-size: 2.5rem; letter-spacing: 1px; margin-bottom: 10px; }
        .brand-text { font-size: 1.1rem; opacity: 0.9; font-weight: 300; }
        .brand-illustration { max-width: 80%; margin-top: 40px; }
        .form-side { display: flex; align-items: center; justify-content: center; padding: 50px; background: #fff; }
        .form-container { width: 100%; max-width: 450px; }
        .form-title { font-weight: 700; color: #333; margin-bottom: 10px; font-size: 2rem; }
        .form-desc { color: #777; margin-bottom: 35px; }
        .custom-input-group { position: relative; margin-bottom: 25px; }
        .custom-input-group i { position: absolute; top: 50%; left: 20px; transform: translateY(-50%); color: #aaa; font-size: 1.2rem; transition: 0.3s; }
        .form-control-custom { width: 100%; padding: 15px 20px 15px 55px; border: 2px solid #eee; border-radius: 12px; font-size: 1rem; transition: 0.3s; background: #f9f9f9; }
        .form-control-custom:focus { border-color: #0d6efd; background: #fff; outline: none; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1); }
        .form-control-custom:focus + i { color: #0d6efd; }
        .btn-gradient { background: linear-gradient(to right, #0d6efd, #00c6ff); border: none; color: white; padding: 15px; border-radius: 12px; font-weight: 600; font-size: 1.1rem; width: 100%; transition: 0.3s; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4); }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #777; text-decoration: none; font-weight: 500; margin-top: 25px; transition: 0.3s; }
        .btn-back:hover { color: #0d6efd; transform: translateX(-5px); }
        .alert { border-radius: 12px; border: none; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d1e7dd; color: #0f5132; }
        .alert-danger { background: #f8d7da; color: #842029; }
        .alert-info { background: #cff4fc; color: #055160; }
        @media (max-width: 991px) { .brand-side { display: none; } .form-side { padding: 30px; } }
    </style>
</head>
<body>

    <div class="container-fluid p-0 overflow-hidden">
        <div class="row row-full-height g-0">
            
            <div class="col-lg-6 brand-side">
                <div class="brand-content">
                    <img src="/TechFixPHP/assets/image/vlute1.png" alt="Logo" class="logo-img">
                    <h1 class="brand-title">TECHFIX</h1>
                    <p class="brand-text">Khôi phục quyền truy cập vào tài khoản của bạn một cách an toàn và nhanh chóng.</p>
                    <img src="https://ouch-cdn2.icons8.com/eB7J8Zq-g6a5s4t4_z8c4h5v7.png" alt="Security Illustration" class="brand-illustration">
                </div>
            </div>

            <div class="col-lg-6 form-side">
                <div class="form-container">
                    <div class="d-lg-none mb-4 text-center">
                        <img src="/TechFixPHP/assets/image/vlute1.png" alt="Logo" width="60">
                        <h3 class="fw-bold mt-2">TECHFIX</h3>
                    </div>

                    <h2 class="form-title">Quên Mật Khẩu? 🔒</h2>
                    <p class="form-desc">Nhập địa chỉ email được liên kết với tài khoản của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>

                    <?php if ($msg): ?>
                        <div class="alert alert-<?= $msg_type ?>" role="alert">
                            <?php if($msg_type == 'success' || $msg_type == 'info') echo '<i class="fa-solid fa-circle-check fa-lg"></i>'; ?>
                            <?php if($msg_type == 'danger') echo '<i class="fa-solid fa-circle-exclamation fa-lg"></i>'; ?>
                            <div><?= $msg ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="custom-input-group">
                            <input type="email" name="email" class="form-control-custom" placeholder="Ví dụ: name@example.com" required autofocus>
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        
                        <button type="submit" class="btn-gradient">
                            Gửi Hướng Dẫn <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="login.php" class="btn-back">
                            <i class="fa-solid fa-arrow-left-long"></i> Quay lại Đăng nhập
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>