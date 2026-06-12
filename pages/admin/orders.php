<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'technical'])) {
    header('Location: /TechFixPHP/pages/public_page/login.php');
    exit;
}

$current_role = $_SESSION['role']; 
include '../../config/db.php';

if ($current_role === 'admin') {
    if (isset($_GET['update']) && isset($_GET['id']) && isset($_GET['status'])) {
        $id = intval($_GET['id']);
        $status = $_GET['status'];
        $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (in_array($status, $allowed_statuses)) {
            $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
        }
        $qs = $_SERVER['QUERY_STRING'];
        header("Location: orders.php?$qs");
        exit;
    }
include_once '../../config/log_helper.php';
    writeLog($conn, "Xóa vĩnh viễn đơn hàng", $id);
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: orders.php");
        exit;
    }
}

include __DIR__ . '/template/sidebar.php';


$limit = 8; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$whereArr = [];
$params = [];
$types = "";

$sort = $_GET['sort'] ?? 'all';
if ($sort !== 'all' && $sort !== '') {
    $whereArr[] = "b.status = ?";
    $params[] = $sort;
    $types .= "s";
}

$cus_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$customer_info = null;

if ($cus_id > 0) {
    $whereArr[] = "b.customer_id = ?";
    $params[] = $cus_id;
    $types .= "i";
    $resInfo = $conn->query("SELECT name, phone FROM users WHERE id = $cus_id");
    if($resInfo->num_rows > 0) $customer_info = $resInfo->fetch_assoc();
}

$whereSql = "";
if (count($whereArr) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereArr);
}

$count_query = "SELECT COUNT(*) as total FROM bookings b $whereSql";
$stmt_count = $conn->prepare($count_query);
if (!empty($params)) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_records = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$query = "
    SELECT 
        b.id, b.status, b.created_at, b.technician_id,
        IFNULL(u.name, '[Khách đã xóa]') AS customer_name,
        IFNULL(s.name, '[Dịch vụ đã xóa]') AS service_name,
        DATE_FORMAT(b.appointment_time, '%d/%m/%Y %H:%i') AS appointment_time,
        t.name as tech_name
    FROM bookings b
    LEFT JOIN users u ON b.customer_id = u.id
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users t ON b.technician_id = t.id
    $whereSql
    ORDER BY b.appointment_time DESC 
    LIMIT ? OFFSET ? 
";

$stmt = $conn->prepare($query);
$types .= "ii"; 
$params[] = $limit;
$params[] = $offset;
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$tech_list = $conn->query("SELECT id, name FROM users WHERE role = 'technical'")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - TechFix</title>
    <link href="/TechFixPHP/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: "Segoe UI", sans-serif; background-color: #f7f9fc; }
        .main-content { margin-left: 250px; padding: 20px; }
        h2 { color: #333; }
        
        .card { border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-header { background: white; font-weight: bold; border-bottom: 1px solid #eee; padding: 15px; }
        table { width: 100%; margin-bottom: 0; }
        th { background: #0d6efd; color: #fff; font-size: 13px; text-align: center; vertical-align: middle; }
        td { font-size: 14px; vertical-align: middle; text-align: center; }
        
        select { padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc; font-size: 13px; }
        .status-select { font-weight: bold; border-radius: 20px; padding: 4px 10px; border: 1px solid #ccc; width: auto; }
        .status-badge { font-weight: bold; border-radius: 20px; padding: 5px 10px; display: inline-block; font-size: 0.8rem; }
        
        .status-pending, .badge-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-confirmed, .badge-confirmed { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .status-completed, .badge-completed { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-cancelled, .badge-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .pagination { justify-content: center; margin-top: 15px; }
        .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
        
        .schedule-box { max-height: 600px; overflow-y: auto; min-height: 200px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-3 col-lg-2 sidebar p-0 collapse d-md-block">
                <?php include __DIR__ . '/template/sidebar.php'; ?>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
                
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h1 class="h3 text-primary">
                        <?php if ($cus_id > 0): ?>
                            <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử sửa chữa
                        <?php else: ?>
                            <i class="fa-solid fa-file-invoice"></i> Quản lý đơn hàng
                        <?php endif; ?>
                    </h1>
                    
                    <div class="d-flex gap-2 align-items-center">
                        <a href="kanban.php" class="btn btn-outline-primary btn-sm fw-bold">
                            <i class="fa-solid fa-table-columns"></i> Xem Kanban
                        </a>

                        <div class="vr"></div> <form method="get" class="d-flex gap-2 align-items-center">
                            <?php if($cus_id > 0): ?> <input type="hidden" name="customer_id" value="<?= $cus_id ?>"> <?php endif; ?>
                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" <?= ($sort == 'all') ? 'selected' : '' ?>>Tất cả trạng thái</option>
                                <option value="pending" <?= ($sort == 'pending') ? 'selected' : '' ?>>Đang chờ</option>
                                <option value="confirmed" <?= ($sort == 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="completed" <?= ($sort == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="cancelled" <?= ($sort == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                            <a href="orders.php?sort=all" class="btn btn-secondary btn-sm" title="Làm mới"><i class="fa-solid fa-rotate"></i></a>
                        </form>
                    </div>
                </div>

                <?php if($cus_id > 0): ?>
                    <div class="alert alert-info py-2 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user"></i> Khách hàng: <b><?= htmlspecialchars($customer_info['name'] ?? 'Unknown') ?></b> (<?= htmlspecialchars($customer_info['phone'] ?? '') ?>)</span>
                        <a href="orders.php" class="btn btn-danger btn-sm">Xem tất cả</a>
                    </div>
                <?php endif; ?>

                <div class="row">
                    
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-list"></i> Danh sách Đơn hàng</span>
                                <span class="badge bg-secondary">Tổng: <?= $total_records ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Mã</th>
                                                <th style="text-align:left">Khách / Dịch vụ</th>
                                                <th>Ngày hẹn</th>
                                                <th>Trạng thái</th>
                                                <th>Tác vụ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td class="fw-bold text-muted">#<?= $row['id'] ?></td>
                                                        <td style="text-align:left">
                                                            <div class="fw-bold"><?= htmlspecialchars($row['customer_name']) ?></div>
                                                            <small class="text-primary"><?= htmlspecialchars($row['service_name']) ?></small>
                                                            <?php if($row['tech_name']): ?>
                                                                <br><small class="text-muted"><i class="fa-solid fa-user-gear"></i> <?= htmlspecialchars($row['tech_name']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="fw-bold text-danger"><?= $row['appointment_time'] ?></td>
                                                        <td>
                                                            <?php if($current_role === 'admin'): ?>
                                                                <select 
                                                                    class="status-select status-<?= htmlspecialchars($row['status']) ?>" 
                                                                    onchange="window.location='orders.php?update=1&id=<?= $row['id'] ?>&status='+this.value + '&customer_id=<?= $cus_id ?>'" 
                                                                    <?= $row['status'] == 'completed' || $row['status'] == 'cancelled' ? 'disabled' : '' ?>
                                                                >
                                                                    <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                                                                    <option value="confirmed" <?= $row['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                                                    <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                                                    <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                                                </select>
                                                            <?php else: ?>
                                                                <?php 
                                                                    $stt = $row['status'];
                                                                    $txt = ($stt=='completed')?'Hoàn thành':(($stt=='confirmed')?'Đã xác nhận':(($stt=='pending')?'Đang chờ':'Đã hủy'));
                                                                ?>
                                                                <span class="status-badge badge-<?= $stt ?>"><?= $txt ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        
                                                        <td>
                                                            <a href="admin_order_detail.php?id=<?= $row['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                               <i class="fa-solid fa-eye"></i>
                                                            </a>

                                                            <?php if($row['status'] == 'completed'): ?>
                                                                <a href="invoice.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-success" title="In hóa đơn">
                                                                   <i class="fa-solid fa-print"></i>
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if($current_role === 'admin'): ?>
                                                                <a href="orders.php?delete=<?= $row['id'] ?>" 
                                                                   onclick="return confirm('Bạn có chắc muốn xóa đơn này?')" 
                                                                   class="btn btn-sm btn-outline-danger" title="Xóa">
                                                                   <i class="fa-solid fa-trash"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center py-4 text-muted">Không có đơn hàng nào.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if ($total_pages > 1): ?>
                                    <div class="card-footer bg-white py-2">
                                        <ul class="pagination pagination-sm mb-0">
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort ?>&customer_id=<?= $cus_id ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header bg-light text-dark">
                                <i class="fa-regular fa-calendar-check"></i> Kiểm tra Lịch rảnh Thợ
                            </div>
                            <div class="card-body">
                                <label class="form-label fw-bold small text-muted">Chọn Kỹ thuật viên để xem lịch:</label>
                                <select id="tech_selector" class="form-select mb-3" onchange="loadTechSchedule(this.value)">
                                    <option value="">-- Chọn thợ --</option>
                                    <?php foreach ($tech_list as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div id="tech_schedule_result" class="schedule-box">
                                    <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-magnifying-glass-chart fa-3x mb-3 opacity-25"></i>
                                        <p class="small">Chọn thợ để xem lịch trình hôm nay và ngày mai.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> </main>
        </div>
    </div>

    <script src="/TechFixPHP/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadTechSchedule(techId) {
            const container = document.getElementById('tech_schedule_result');
            if (!techId) {
                container.innerHTML = '<div class="text-center text-muted py-4">Chọn thợ để xem lịch...</div>';
                return;
            }
            
            container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
            
            fetch('fetch_schedule.php?technician_id=' + techId)
                .then(res => res.text())
                .then(html => { container.innerHTML = html; })
                .catch(err => { container.innerHTML = '<p class="text-danger text-center">Lỗi tải lịch.</p>'; });
        }
    </script>
</body>
</html>