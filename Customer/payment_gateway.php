<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'customer') {
    die("Vui lòng đăng nhập để thanh toán.");
}

require_once '../config/db.php';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id == 0) {
    die("ID đơn hàng không hợp lệ.");
}


$customer_id = $_SESSION['user']['id'];
$sql = "SELECT * FROM bookings WHERE id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập.");
}

if ($booking['status'] == 'paid') {
    echo "<script>alert('Đơn hàng này đã được thanh toán!'); window.location.href='my_booking.php';</script>";
    exit;
}


$bankId = 'MB';             
$accountNo = '0000123456789'; 
$accountName = 'TECHFIX SYSTEM'; 
$amount = $booking['final_price'];
$content = "TECHFIX $booking_id"; 

$qr_url = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.jpg?amount={$amount}&addInfo={$content}&accountName={$accountName}";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán TechFix - Đơn #<?= $booking_id ?></title>
    
    <link href="/TechFixPHP/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #e9ecef; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .payment-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; width: 100%; max-width: 420px; position: relative; }
        .card-header { background: #0d6efd; padding: 20px; text-align: center; color: white; border: none; }
        .card-body { padding: 30px; text-align: center; }
        .amount-box { background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0; border: 1px dashed #ced4da; }
        .amount-text { font-size: 1.8rem; font-weight: 800; color: #198754; margin: 0; }
        .qr-frame { border: 2px solid #e9ecef; border-radius: 12px; padding: 10px; display: inline-block; margin-bottom: 20px; background: white; }
        .qr-img { width: 100%; max-width: 250px; border-radius: 8px; }
        .timer-text { font-size: 0.85rem; color: #6c757d; margin-top: 10px; }
        .btn-demo { font-size: 0.8rem; text-decoration: underline; color: #6c757d; margin-top: 15px; background: none; border: none; cursor: pointer; }
        .btn-demo:hover { color: #0d6efd; }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="card-header">
        <h5 class="m-0 fw-bold"><i class="fa-solid fa-shield-halved"></i> Cổng Thanh Toán An Toàn</h5>
        <small class="opacity-75">Đơn hàng #<?= $booking_id ?></small>
    </div>

    <div class="card-body">
        <p class="text-muted mb-2">Quét mã QR bằng ứng dụng ngân hàng</p>
        
        <div class="qr-frame">
            <img src="<?= $qr_url ?>" class="qr-img" alt="Mã QR VietQR">
        </div>

        <div class="amount-box">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Số tiền cần thanh toán</small>
            <p class="amount-text"><?= number_format($amount, 0, ',', '.') ?> đ</p>
        </div>

        <div class="alert alert-info d-flex align-items-center small p-2" role="alert">
            <i class="fa-solid fa-circle-info me-2 fa-lg"></i>
            <div class="text-start">Nội dung CK: <strong><?= $content ?></strong><br>(Hệ thống tự động ghi nhận sau 3s)</div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button id="btnCheck" class="btn btn-primary fw-bold py-2" onclick="manualCheck()">
                <i class="fa-solid fa-check-to-slot"></i> Tôi đã chuyển khoản xong
            </button>
            
            <a href="my_booking.php" class="btn btn-outline-secondary btn-sm border-0">
                <i class="fa-solid fa-arrow-left"></i> Để sau, quay lại
            </a>
        </div>

        <button class="btn-demo" onclick="simulatePayment(this)">
            (Demo) Giả lập "Tiền đã về" ngay lập tức
        </button>
    </div>
</div>

<script>
    const bookingId = <?= $booking_id ?>;
    let isPaid = false;

    function checkPaymentStatus(silent = true) {
        if (isPaid) return; 

fetch('/TechFixPHP/pages/api/check_payment_status.php?id=' + bookingId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'paid') {
                    isPaid = true;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Thanh toán thành công!',
                        text: 'Cảm ơn bạn đã sử dụng dịch vụ TechFix.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'my_booking.php'; 
                    });
                } else if (!silent) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Đang chờ xử lý...',
                        text: 'Hệ thống chưa nhận được tin nhắn từ ngân hàng. Vui lòng đợi thêm giây lát.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => console.error("Lỗi kiểm tra:", err));
    }

    function manualCheck() {
        const btn = document.getElementById('btnCheck');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang kiểm tra...';
        
        checkPaymentStatus(false);

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-to-slot"></i> Tôi đã chuyển khoản xong';
        }, 3000);
    }

    function simulatePayment(btnElement) {
        Swal.fire({
            title: 'Chế độ Demo',
            text: "Bạn muốn giả lập ngân hàng báo tiền về ngay lập tức?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đúng, Demo luôn!',
            cancelButtonText: 'Không'
        }).then((result) => {
            if (result.isConfirmed) {
                const originalText = btnElement.innerText;
                btnElement.innerText = "Đang kết nối ngân hàng ảo...";
                btnElement.disabled = true;

                fetch(`/TechFixPHP/pages/api/simulate_ipn.php?id=${bookingId}`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            checkPaymentStatus(false);
                        } else {
                            Swal.fire('Lỗi Demo', data.message || 'Không thể cập nhật DB.', 'error');
                            btnElement.innerText = originalText;
                            btnElement.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Lỗi Kết Nối', 'Không gọi được API giả lập (Kiểm tra file simulate_ipn.php)', 'error');
                        btnElement.innerText = originalText;
                        btnElement.disabled = false;
                    });
            }
        });
    }

    setInterval(() => checkPaymentStatus(true), 3000);

</script>

</body>
</html>