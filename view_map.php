<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';

$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bookingId <= 0) {
    die("Mã đơn hàng không hợp lệ!");
}

$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Không tìm thấy đơn hàng!");
}

$hasLocation = !empty($booking['lat']) && !empty($booking['lng']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFix Map - Theo dõi đơn hàng #<?= $booking['id'] ?></title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #0d6efd; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        .info-box { background: #e7f1ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #0d6efd; }
        .info-row { margin-bottom: 8px; font-size: 15px; }
        #map { height: 550px; width: 100%; border-radius: 12px; border: 2px solid #dee2e6; z-index: 1; }
        .badge { background: #28a745; color: white; padding: 3px 8px; border-radius: 6px; font-size: 12px; vertical-align: middle; }
        .error { color: #dc3545; font-weight: bold; background: #f8d7da; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>🗺️ Lộ Trình Di Chuyển - Đơn #<?= $booking['id'] ?></h2>
    
    <div class="info-box">
        <div class="info-row"><strong>👤 Khách hàng:</strong> <?= htmlspecialchars($booking['customer_name'] ?? 'Khách hàng') ?></div>
        <div class="info-row"><strong>📍 Địa chỉ:</strong> <?= htmlspecialchars($booking['address']) ?>, <?= htmlspecialchars($booking['district']) ?></div>
        
        <?php if ($hasLocation): ?>
            <div class="info-row"><strong>📡 Tọa độ:</strong> <?= $booking['lat'] ?>, <?= $booking['lng'] ?> <span class="badge">Đã định vị</span></div>
        <?php else: ?>
            <div class="error">⚠️ Đơn này chưa có tọa độ (Không thể hiển thị bản đồ).</div>
        <?php endif; ?>
    </div>

    <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        const shopLat = 10.254227; 
        const shopLng = 105.972428;

        <?php if ($hasLocation): ?>
            const customerLat = <?= $booking['lat'] ?>;
            const customerLng = <?= $booking['lng'] ?>;
        <?php else: ?>
            document.getElementById('map').innerHTML = '<h3 style="text-align:center; padding-top: 200px; color: gray;">Không có dữ liệu bản đồ</h3>';
            return; 
        <?php endif; ?>

        var map = L.map('map'); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© TechFix Map'
        }).addTo(map);

        var shopIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3600/3600967.png',
            iconSize: [45, 45], iconAnchor: [22, 45], popupAnchor: [0, -40]
        });

        var userIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/619/619153.png', 
            iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
        });

        var techIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3063/3063823.png',
            iconSize: [50, 50], iconAnchor: [25, 25], popupAnchor: [0, -30]
        });

        L.marker([shopLat, shopLng], {icon: shopIcon}).addTo(map)
            .bindPopup("<b>🏢 Trụ sở TechFix</b><br>KTV xuất phát.");

        L.marker([customerLat, customerLng], {icon: userIcon}).addTo(map)
            .bindPopup("<b>🏠 Nhà khách hàng</b><br><?= htmlspecialchars($booking['customer_name'] ?? 'Khách hàng') ?>");

        var latlngs = [[shopLat, shopLng], [customerLat, customerLng]];
        var polyline = L.polyline(latlngs, {
            color: '#adb5bd', 
            weight: 4, 
            dashArray: '10, 10' 
        }).addTo(map);

        map.fitBounds(polyline.getBounds(), {padding: [80, 80]});

        
        var techMarker = L.marker([shopLat, shopLng], {icon: techIcon}).addTo(map)
            .bindPopup("<b>🛵 Kỹ thuật viên</b><br>Đang di chuyển...");

        var passedPath = L.polyline([], {color: '#0d6efd', weight: 5}).addTo(map);

      
        let steps = 200; 
        let stepCount = 0;
        let deltaLat = (customerLat - shopLat) / steps;
        let deltaLng = (customerLng - shopLng) / steps;

        function moveTech() {
            if (stepCount <= steps) {
                let curLat = shopLat + (deltaLat * stepCount);
                let curLng = shopLng + (deltaLng * stepCount);
                let newPos = [curLat, curLng];

                techMarker.setLatLng(newPos);
                
                passedPath.addLatLng(newPos);

                stepCount++;
                
                requestAnimationFrame(moveTech);
            } else {
                techMarker.bindPopup("<b>✅ Đã đến nơi!</b>").openPopup();
            }
        }

        setTimeout(moveTech, 1000);
    });
</script>

</body>
</html>