<?php
session_start();
include '../../config/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: /TechFixPHP/pages/public_page/login.php');
    exit;
}

$user = $_SESSION['user'];
$msg = "";
$msg_type = "";

// 2. Xử lý khi bấm Lưu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']); 
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $new_pass = $_POST['password']; // Mật khẩu mới (nếu có)

    $avatarPath = null;

    // --- XỬ LÝ UPLOAD ẢNH (Bảo mật hơn) ---
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $uploadDir = '../../assets/upload/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // Đặt tên file ngẫu nhiên để tránh trùng
            $newFileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            $targetFile = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $avatarPath = 'assets/upload/' . $newFileName;
            }
        } else {
            $msg = "Chỉ cho phép upload file ảnh (JPG, PNG, GIF)!";
            $msg_type = "error";
        }
    }

    if (empty($msg)) { // Nếu không có lỗi upload
        // --- XÂY DỰNG CÂU SQL ĐỘNG ---
        // Lý do: Nếu user không nhập pass hoặc không up ảnh thì không được update cột đó
        
        $sql = "UPDATE users SET name=?, email=?, phone=?, address=?";
        $types = "ssss";
        $params = [$name, $email, $phone, $address];

        // Nếu có nhập pass mới -> Mã hóa và Update
        if (!empty($new_pass)) {
            $sql .= ", password=?";
            $types .= "s";
            $params[] = password_hash($new_pass, PASSWORD_DEFAULT); // 
        }

        // Nếu có up ảnh mới -> Update
        if ($avatarPath) {
            $sql .= ", avatar=?";
            $types .= "s";
            $params[] = $avatarPath;
        }

        $sql .= " WHERE id=?";
        $types .= "i";
        $params[] = $user['id'];

        // Thực thi SQL
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // --- CẬP NHẬT LẠI SESSION TỪ DB (Chuẩn nhất) ---
            $stmt_refresh = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_refresh->bind_param("i", $user['id']);
            $stmt_refresh->execute();
            $newUser = $stmt_refresh->get_result()->fetch_assoc();
            
            $_SESSION['user'] = $newUser; // Cập nhật session toàn bộ
            $user = $newUser; // Cập nhật biến hiển thị
            
            $msg = "✅ Cập nhật hồ sơ thành công!";
            $msg_type = "success";
        } else {
            $msg = "❌ Lỗi hệ thống: " . $conn->error;
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân - TechFix</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .profile-page { display: flex; justify-content: center; padding: 40px; min-height: 100vh; align-items: center; }
        .profile-container { display: flex; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 900px; max-width: 100%; }
        
        /* Cột Trái */
        .profile-left { width: 35%; background: linear-gradient(135deg, #0078d7, #005a9e); color: white; text-align: center; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .avatar-wrapper { position: relative; display: inline-block; margin-bottom: 15px; }
        .avatar-img, .avatar-circle { width: 140px; height: 140px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); object-fit: cover; }
        .avatar-circle { background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 50px; }
        
        .change-avatar-btn { position: absolute; bottom: 5px; right: 5px; background: #28a745; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white; transition: 0.2s; }
        .change-avatar-btn:hover { transform: scale(1.1); }
        
        /* Cột Phải */
        .profile-right { flex: 1; padding: 40px; }
        .form-title { color: #333; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        .form-group { margin-bottom: 20px; position: relative; }
        .icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666; }
        .form-control { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e1e1e1; border-radius: 8px; box-sizing: border-box; }
        .form-control:focus { border-color: #0078d7; outline: none; box-shadow: 0 0 0 3px rgba(0,120,215,0.1); }
        
        .btn-group { display: flex; justify-content: space-between; margin-top: 30px; }
        .btn-save { background: #0078d7; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background: #005fa3; }
        .btn-back { text-decoration: none; color: #666; display: flex; align-items: center; }

        /* Thông báo */
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="profile-page">
    <div class="profile-container">
            
        <div class="profile-left">
            <div class="avatar-wrapper">
                <?php 
                $avatarSrc = '';
                if (!empty($user['avatar'])) {
                    $avatarSrc = "/TechFixPHP/" . $user['avatar']; // Ảnh upload local
                } elseif (!empty($user['picture'])) { // Trường hợp login Google (nếu có lưu cột picture)
                    $avatarSrc = $user['picture'];
                }
                ?>

                <?php if ($avatarSrc): ?>
                    <img src="<?= htmlspecialchars($avatarSrc) ?>" class="avatar-img" alt="Avatar">
                <?php else: ?>
                    <div class="avatar-circle"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                <?php endif; ?>

                <label class="change-avatar-btn" for="avatarInput" title="Thay đổi ảnh">📷</label>
            </div>
            
            <input type="file" id="avatarInput" name="avatar" form="profileForm" accept="image/*" style="display:none">
            
            <h2 class="user-name"><?= htmlspecialchars($user['name']) ?></h2>
            <span class="user-role badge"><?= ucfirst($user['role'] ?? 'Member') ?></span>
        </div>

        <div class="profile-right">
            <h2 class="form-title">Cập nhật thông tin</h2>
            
            <?php if ($msg): ?>
                <div class="alert <?= $msg_type ?>"><?= $msg ?></div>
            <?php endif; ?>

            <form id="profileForm" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <span class="icon">👤</span>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Họ và tên" required>
                </div>

                <div class="form-group">
                    <span class="icon">📧</span>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="background: #e9ecef; cursor: not-allowed;" title="Không thể đổi email">
                </div>

                <div class="form-group">
                    <span class="icon">📱</span>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Số điện thoại">
                </div>

                <div class="form-group">
                    <span class="icon">🔒</span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới (Chỉ điền nếu muốn đổi)">
                </div>

                <div class="form-group">
                    <span class="icon">📍</span>
                    <textarea name="address" class="form-control" placeholder="Địa chỉ giao hàng mặc định"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="btn-group">
                    <a href="/TechFixPHP/index.php" class="btn-back">⬅️ Quay lại trang chủ</a>
                    <button type="submit" class="btn-save">💾 Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Xem trước ảnh khi chọn
document.getElementById('avatarInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.querySelector('.avatar-wrapper');
            // Tìm thẻ img hiện có hoặc tạo mới nếu đang hiển thị chữ cái
            let img = wrapper.querySelector('.avatar-img');
            if (!img) {
                // Nếu chưa có thẻ img (đang hiện avatar-circle), xóa circle và tạo img
                const circle = wrapper.querySelector('.avatar-circle');
                if(circle) circle.remove();
                
                img = document.createElement('img');
                img.className = 'avatar-img';
                wrapper.insertBefore(img, wrapper.querySelector('.change-avatar-btn'));
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>