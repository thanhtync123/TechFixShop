<?php
session_start();
include '../../config/db.php'; 

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'technical') {
    header("Location: /TechFixPHP/pages/public_page/login.php");
    exit();
}

$tech_id = $_SESSION['user']['id']; 

$bookings_query = $conn->prepare("
    SELECT 
        b.id, b.customer_name, b.phone, b.address, b.appointment_time, b.status, b.final_price,
        s.name AS service_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    WHERE b.technician_id = ? AND b.status = 'completed'
    ORDER BY b.appointment_time DESC
");

$bookings_query->bind_param("i", $tech_id);
$bookings_query->execute();
$result = $bookings_query->get_result();
$bookings = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$total_revenue = 0;
foreach ($bookings as $b) {
    $total_revenue += $b['final_price'];
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử công việc - TECHFIX</title>
    
    <link rel="stylesheet" href="../../assets/css/tech_history.css"> 
</head>
<body>

<?php 

include __DIR__ . '/../admin/template/sidebar.php'; 
?>

<main class="main-content">

    <div class="container-widget"> 
        <h2>Lịch sử công việc (Đã Hoàn thành)</h2>
        <p><strong>Kỹ thuật viên:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>

        <?php if (!empty($bookings)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách hàng</th>
                        <th>Địa chỉ</th>
                        <th>Dịch vụ</th>
                        <th>Ngày hoàn thành</th>
                        <th>Giá tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                            <td><?= htmlspecialchars($booking['address']) ?></td>
                            <td><?= htmlspecialchars($booking['service_name']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($booking['appointment_time'])) ?></td>
                            <td style="font-weight: bold;"><?= number_format($booking['final_price']) ?>đ</td>
                            <td><span class="status"><?= ucfirst($booking['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-bar">
                Tổng doanh thu (từ các việc đã hoàn thành): <?= number_format($total_revenue) ?>đ
            </div>

        <?php else: ?>
            <p class="no-booking-message">Bạn chưa hoàn thành công việc nào.</p>
        <?php endif; ?>
    </div>
</main> </body>
</html>