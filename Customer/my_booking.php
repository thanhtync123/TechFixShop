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
        SELECT b.id, b.appointment_time, b.final_price, b.status, b.created_at,
               b.lat, b.lng, b.payment_status, s.name AS service_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.customer_id = ?
        ORDER BY b.created_at DESC
    ";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

$pageTitle = 'Lịch đặt của tôi - TECHFIX';
include __DIR__ . '/template/header.php';
?>

<style>
.mb-page { max-width: 1100px; margin: 30px auto; padding: 0 16px; }
.mb-page h2 { text-align: center; color: var(--tf-text, #1e293b); margin-bottom: 20px; font-size: 1.6rem; }
.mb-table-wrap { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; }
.mb-table { width: 100%; border-collapse: collapse; text-align: center; }
.mb-table th, .mb-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.mb-table th { background: #1d4ed8; color: #fff; font-size: 0.85rem; font-weight: 600; }
.mb-table tbody tr:hover { background: #f8faff; }
.mb-table td { font-size: 0.9rem; color: #374151; }
.cash-hint { font-size: 11px; color: #666; font-style: italic; display: block; margin-top: 4px; }
.done-hint { font-size: 11px; color: #16a34a; font-weight: bold; display: block; margin-top: 4px; }
.detail-link { color: #1d4ed8; font-weight: 500; }
.detail-link:hover { text-decoration: underline; }
.map-btn { display: inline-block; margin-top: 5px; padding: 3px 8px; background: #eff6ff; color: #1d4ed8; border-radius: 5px; font-size: 11px; font-weight: bold; border: 1px solid #bfdbfe; }
.qr-btn { display: inline-block; margin-top: 5px; padding: 3px 8px; background: #dcfce7; color: #16a34a; border-radius: 5px; font-size: 11px; font-weight: bold; border: 1px solid #bbf7d0; cursor: pointer; }
.no-booking { text-align: center; padding: 50px; color: #94a3b8; font-size: 1rem; }
/* Notification */
#notificationBell { position: fixed; bottom: 80px; right: 24px; font-size: 26px; cursor: pointer; color: #1d4ed8; z-index: 1000; background: #fff; width: 50px; height: 50px; border-radius: 50%; display: grid; place-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
#notificationBell .badge { background: #dc2626; color: #fff; font-size: 11px; padding: 2px 5px; border-radius: 50%; position: absolute; top: 2px; right: 2px; }
#notificationPopup { display: none; position: fixed; bottom: 140px; right: 24px; width: 320px; background: #fff; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.12); z-index: 999; }
#notificationPopup .list { max-height: 300px; overflow-y: auto; padding: 0; margin: 0; list-style: none; }
#notificationPopup .list li { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
#notificationPopup .list li.unread { background: #eff6ff; font-weight: 600; }
</style>

<div class="mb-page">
    <h2><i class="fa-solid fa-calendar-days" style="color:#1d4ed8"></i> Lịch đặt dịch vụ của tôi</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="mb-table-wrap">
        <table class="mb-table">
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
                        
                        <td style="color:#dc2626; font-weight: bold;">
                            <?= number_format($row['final_price'], 0, ',', '.') ?>đ
                        </td>

                        <td>
                            <?php
                                $stt = $row['status'];
                                $statusMap = [
                                    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'tf-badge tf-badge-pending'],
                                    'confirmed' => ['label' => 'Đã có thợ',    'class' => 'tf-badge tf-badge-confirmed'],
                                    'in_progress'=> ['label'=> 'Đang làm',     'class' => 'tf-badge tf-badge-confirmed'],
                                    'fixing'    => ['label' => 'Đang làm',     'class' => 'tf-badge tf-badge-confirmed'],
                                    'completed' => ['label' => 'Hoàn thành',   'class' => 'tf-badge tf-badge-completed'],
                                    'paid'      => ['label' => 'Đã xong',      'class' => 'tf-badge tf-badge-completed'],
                                    'cancelled' => ['label' => 'Đã hủy',       'class' => 'tf-badge tf-badge-cancelled'],
                                ];
                                $info = $statusMap[$stt] ?? ['label' => ucfirst($stt), 'class' => 'tf-badge'];
                            ?>
                            <span class="<?= $info['class'] ?>"><?= $info['label'] ?></span>

                            <?php if (in_array($stt, ['completed','paid']) || $row['payment_status'] === 'paid'): ?>
                                <span class="done-hint"><i class="fa-solid fa-check"></i> Đã thanh toán</span>
                            <?php elseif ($stt !== 'cancelled'): ?>
                                <span class="cash-hint">Thanh toán tiền mặt sau khi xong</span>
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
        </div>
    <?php else: ?>
        <div class="no-booking">
            <i class="fa-regular fa-calendar-xmark" style="font-size:3rem; display:block; margin-bottom:12px; color:#cbd5e1;"></i>
            Bạn chưa có lịch đặt nào.
            <br><a href="/TechFixPHP/Customer/book.php" class="tf-btn tf-btn-primary" style="margin-top:16px; display:inline-flex;">
                <i class="fa-solid fa-calendar-plus"></i> Đặt lịch ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<div id="notificationBell">
    🔔 <span class="badge" id="notificationCount">0</span>
</div>
<div id="notificationPopup">
    <ul class="list" id="notificationList"><li>Đang tải...</li></ul>
</div>
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