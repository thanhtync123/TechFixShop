<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'customer') {
    header("Location: /TechFixPHP/pages/public_page/login.php");
    exit();
}

include '../config/db.php';
$user = $_SESSION['user'] ?? null;
$customer_id = $user['id'] ?? null;

$result = false;

if ($customer_id && isset($conn)) {
    $query = "
        SELECT 
            b.id, 
            b.appointment_time, 
            b.final_price,  
            b.status, 
            b.created_at,
            b.lat, 
            b.lng,
            b.payment_status,
            s.name AS service_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.customer_id = ?
        ORDER BY b.created_at DESC
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("<pre>Lỗi SQL: " . $conn->error . "</pre>");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lịch đặt của tôi - TECHFIX</title>
<link rel="stylesheet" href="../../assets/css/customer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body{background:#f5f6fa;font-family:'Poppins',sans-serif}
    .container{max-width:1000px;margin:60px auto;background:#fff;border-radius:10px;padding:30px;box-shadow:0 3px 10px rgba(0,0,0,0.1);position:relative}
    h2{text-align:center;color:#333;margin-bottom:20px}
    table{width:100%;border-collapse:collapse;text-align:center}
    th,td{padding:12px;border-bottom:1px solid #ddd; vertical-align: middle;} 
    th{background:#0099ff;color:#fff}
    
    .status{padding:5px 12px;border-radius:8px;font-weight:600;display:inline-block;min-width:110px; font-size: 0.85rem;}
    
    .pending{background:#ffeb3b;color:#333} 
    .confirmed{background:#17a2b8;color:#fff} 
    .fixing{background:#fd7e14;color:#fff} 
    
    .completed{background:#28a745;color:#fff} 
    .paid{background:#28a745;color:#fff} 
    
    .cancelled{background:#f44336;color:#fff} 

    .cash-hint { font-size: 11px; color: #666; font-style: italic; display: block; margin-top: 4px; }
    .done-hint { font-size: 11px; color: #28a745; font-weight: bold; display: block; margin-top: 4px; }

    .detail-link{color:#0099ff;text-decoration:none;font-weight:500}
    .detail-link:hover{text-decoration:underline}
    .back-home{text-decoration: none; display: inline-block; margin-bottom: 15px; color: #555;}

    .map-btn {
        display: inline-block; margin-top: 5px; padding: 4px 8px;
        background-color: #e7f1ff; color: #0d6efd; border-radius: 5px;
        text-decoration: none; font-size: 11px; font-weight: bold;
        border: 1px solid #0d6efd; transition: 0.3s;
    }
    .map-btn:hover { background-color: #0d6efd; color: white; }

    .qr-btn {
        display: inline-block; margin-top: 5px; padding: 4px 8px;
        background-color: #28a745; color: white; border-radius: 5px;
        text-decoration: none; font-size: 11px; font-weight: bold;
        border: 1px solid #28a745; transition: 0.3s; cursor: pointer;
    }
    .qr-btn:hover { background-color: #218838; }

    #notificationBell{position:fixed;top:20px;right:30px;font-size:28px;cursor:pointer;color:#0099ff; z-index: 1000;}
    #notificationBell .badge{background:red;color:#fff;font-size:12px;padding:2px 6px;border-radius:50%;position:absolute;top:-5px;right:-8px}
    #notificationPopup{display:none;position:fixed;top:60px;right:30px;width:320px;background:#fff;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);z-index:999}
    #notificationPopup .list{max-height:300px;overflow-y:auto;padding:0;margin:0;list-style:none}
    #notificationPopup .list li{padding:10px;border-bottom:1px solid #eee;font-size:14px}
    #notificationPopup .list li.unread{background:#f0f8ff;font-weight:700}
    
    .no-booking{text-align:center;padding:30px;color:#777;font-size:16px}
</style>
</head>
<body>

<div id="notificationBell">
    🔔 <span class="badge" id="notificationCount">0</span>
</div>
<div id="notificationPopup">
    <ul class="list" id="notificationList"><li>Đang tải...</li></ul>
</div>

<div class="container">
    <h2>📅 Lịch đặt dịch vụ của tôi</h2>
    <a href="/TechFixPHP/index.php" class="back-home">🏠 Quay lại trang chủ</a>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên dịch vụ</th>
                    <th>Ngày hẹn</th>
                    <th>Chi phí</th>
                    <th>Trạng thái</th> 
                    <th>Ngày đặt</th>
                    <th>Chi tiết</th> 
                    <th>Đánh giá</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['service_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['appointment_time'])) ?></td>
                        
                        <td style="color: #d9534f; font-weight: bold;">
                            <?= number_format($row['final_price'], 0, ',', '.') ?>đ
                        </td>

                        <td>
                            <span class="status <?= $row['status'] ?>">
                                <?php 
                                    $statusMap = [
                                        'pending'   => 'Chờ xác nhận',
                                        'confirmed' => 'Đã có thợ',
                                        'in_progress' => 'Đang thực hiện', 
                                        'fixing'    => 'Đang thực hiện',
                                        'completed' => 'Hoàn thành', 
                                        'paid'      => 'Đã xong', 
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    echo $statusMap[$row['status']] ?? ucfirst($row['status']);
                                ?>
                            </span>

                            <?php 
                                if ($row['status'] === 'completed' || $row['status'] === 'paid' || $row['payment_status'] === 'paid'): 
                            ?>
                                <span class="done-hint">
                                    <i class="fa-solid fa-check"></i> Đã thanh toán
                                </span>
                            
                            <?php 
                                elseif ($row['status'] !== 'cancelled'): 
                            ?>
                                <span class="cash-hint">
                                    Thanh toán tiền mặt sau khi xong
                                </span>
                            <?php endif; ?>
                        </td>

                        <td><?= date('d/m H:i', strtotime($row['created_at'])) ?></td>
                        
                        <td>
                            <a href="booking_detail.php?id=<?= $row['id'] ?>" class="detail-link">Xem chi tiết</a>
                            
                            <?php if (!empty($row['lat']) && !empty($row['lng']) && $row['status'] !== 'completed' && $row['status'] !== 'cancelled'): ?>
                                <br>
                                <a href="/TechFixPHP/view_map.php?id=<?= $row['id'] ?>" target="_blank" class="map-btn">
                                    📍 Theo dõi thợ
                                </a>
                            <?php endif; ?>

                            <?php if ($row['status'] === 'confirmed'): 
                                $qrData = "TECHFIX_ORDER_" . $row['id'];
                                $qrImage = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $qrData;
                            ?>
                                <br>
                                <button onclick="showQR('<?= $qrImage ?>', '<?= $row['id'] ?>')" class="qr-btn">
                                    <i class="fa-solid fa-qrcode"></i> Mã Check-in
                                </button>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <?php if ($row['status'] === 'completed' || $row['status'] === 'paid'): ?>
                                <a href="reviews.php?booking_id=<?= $row['id'] ?>" style="color:#ff9800; text-decoration: none; font-weight:bold;">
                                    ⭐ Đánh giá
                                </a>
                            <?php else: ?>
                                <span style="color:#ccc;">...</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-booking">😕 Bạn chưa có lịch đặt nào.</p>
    <?php endif; ?>
</div>

<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; text-align:center; max-width:90%; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: fadeIn 0.3s;">
        <h3 style="margin-top:0; color: #333;">Đưa mã này cho thợ quét</h3>
        <p style="color:#666; font-size:14px;">Để xác nhận thợ đã đến nơi và bắt đầu làm việc</p>
        
        <img id="qrImageSrc" src="" style="width:200px; height:200px; margin: 15px 0; border: 1px solid #ddd; padding: 5px;">
        
        <p style="font-size: 16px;">Mã đơn: #<b id="qrOrderId" style="color: #0099ff;"></b></p>
        
        <button onclick="document.getElementById('qrModal').style.display='none'" style="padding: 8px 25px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Đóng</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function showQR(url, id) {
    document.getElementById('qrImageSrc').src = url;
    document.getElementById('qrOrderId').innerText = id;
    document.getElementById('qrModal').style.display = 'flex';
}

window.onclick = function(event) {
    var modal = document.getElementById('qrModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function loadNotifications() {
    $.ajax({
        url: "fetch_notifications.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            let html = "";
            let unread = 0;
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(n => {
                    const cls = n.status === "unread" ? "unread" : "";
                    html += `<li class="${cls}">${escapeHtml(n.message)}<br><small style="color:gray">${n.created_at}</small></li>`;
                    if (n.status === "unread") unread++;
                });
            } else {
                html = "<li>Không có thông báo nào.</li>";
            }
            $("#notificationList").html(html);
            $("#notificationCount").text(unread);
        },
        error: function() {
            $("#notificationList").html("<li>Không thể tải thông báo.</li>");
        }
    });
}

function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}

$(function() {
    const bell = $("#notificationBell");
    const popup = $("#notificationPopup");

    bell.on("click", function() {
        popup.toggle();
        loadNotifications();
    });

    $(document).on("click", function(e) {
        if (!bell.is(e.target) && bell.has(e.target).length === 0 && !popup.is(e.target) && popup.has(e.target).length === 0) {
            popup.hide();
        }
    });

    loadNotifications();
    setInterval(loadNotifications, 10000);
});
</script>
<style>
@keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
</style>
</body>
</html>