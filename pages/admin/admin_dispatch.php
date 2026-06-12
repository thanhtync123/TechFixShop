<?php
session_start();
include '../../config/db.php'; 

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: /TechFixPHP/pages/public_page/login.php");
    exit();
}



$tech_list = [];
$tech_query = $conn->query("SELECT id, name FROM users WHERE role = 'technical'");
if ($tech_query) {
    $tech_list = $tech_query->fetch_all(MYSQLI_ASSOC);
}

$search_term = $_GET['search'] ?? '';
$sql_where = ""; 
$params = []; 

if (!empty($search_term)) {
    $like_term = "%" . $search_term . "%";
    $sql_where = " WHERE (b.customer_name LIKE ? OR b.phone LIKE ? OR b.id = ?) ";
    $params[] = $like_term;
    $params[] = $like_term;
    $params[] = $search_term;
}


$limit = 10; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$count_sql = "SELECT COUNT(*) FROM bookings b " . $sql_where;
$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt_count->execute();
$stmt_count->bind_result($total_records);
$stmt_count->fetch();
$stmt_count->close();

$total_pages = max(1, ceil($total_records / $limit));

$query_sql = "
    SELECT 
        b.id, b.customer_name, b.address, b.appointment_time, b.status, b.technician_id,
        b.final_price, b.district, b.phone,
        IFNULL(s.name, '[Dịch vụ đã xóa]') AS service_name,
        t.name AS tech_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users t ON b.technician_id = t.id
    $sql_where
    ORDER BY 
        CASE WHEN b.status = 'pending' THEN 1 ELSE 2 END, 
        b.appointment_time ASC
    LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($query_sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$bookings_query = $stmt->get_result();
$bookings = $bookings_query ? $bookings_query->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trung tâm Điều phối - TECHFIX</title>
    
    <link href="/TechFixPHP/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; font-size: 0.9rem; }
        
        .sidebar { min-height: 100vh; background: #343a40; color: white; }
        .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; display: block; padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a:hover { background: #0d6efd; }

        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-header { background-color: #fff; border-bottom: 1px solid #eee; padding: 15px 20px; font-weight: 700; color: #495057; }

        .table th { background-color: #f8f9fa; color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; padding: 15px; vertical-align: middle; }
        .table td { vertical-align: middle; padding: 12px 15px; color: #333; }
        
        .badge-status { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; display: inline-block; }
        .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-confirmed { background: #cff4fc; color: #055160; border: 1px solid #b6effb; }
        .status-completed { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .status-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .assign-form { display: flex; align-items: center; gap: 5px; }
        .assign-form select { max-width: 150px; font-size: 0.85rem; padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc; }
        .assign-form button { padding: 4px 10px; font-size: 0.85rem; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; transition: 0.2s; }
        .assign-form button:hover { background: #0b5ed7; }

        .sticky-widget { position: sticky; top: 20px; }
        .schedule-box { max-height: 500px; overflow-y: auto; background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 10px; min-height: 200px; }
        
        .search-input { border-radius: 20px 0 0 20px; border-right: none; }
        .search-btn { border-radius: 0 20px 20px 0; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-3 col-lg-2 sidebar p-0 collapse d-md-block">
            <?php include __DIR__ . '/template/sidebar.php'; ?>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 fw-bold text-primary mb-0"><i class="fa-solid fa-tower-broadcast"></i> Trung Tâm Điều Phối</h2>
                    <p class="text-muted small mb-0">Phân công đơn hàng cho kỹ thuật viên</p>
                </div>
                
                <form method="GET" action="admin_dispatch.php" class="d-flex bg-white p-1 rounded-pill shadow-sm border">
                    <input type="text" name="search" class="form-control form-control-sm border-0 search-input ps-3" placeholder="Tìm khách, SĐT, ID..." value="<?= htmlspecialchars($search_term) ?>" style="width: 250px;">
                    <button type="submit" class="btn btn-primary btn-sm search-btn px-3"><i class="fa-solid fa-search"></i></button>
                    <?php if(!empty($search_term)): ?>
                        <a href="admin_dispatch.php" class="btn btn-light btn-sm text-danger rounded-circle ms-1" title="Xóa lọc"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="row">
                
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-list-ul"></i> Danh sách Đơn hàng cần xử lý</span>
                            <span class="badge bg-secondary rounded-pill">Tổng: <?= $total_records ?></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">ID</th>
                                            <th>Khách hàng / Địa chỉ</th>
                                            <th>Dịch vụ / Thời gian</th>
                                            <th>Trạng thái</th>
                                            <th>Phân công</th>
                                            <th class="text-center" width="60">In</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($bookings)): ?>
                                            <?php foreach ($bookings as $booking): ?>
                                                <tr>
                                                    <td class="text-center fw-bold text-muted">#<?= $booking['id'] ?></td>
                                                    
                                                    <td>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($booking['customer_name']) ?></div>
                                                        <small class="text-muted"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($booking['phone']) ?></small>
                                                        <div class="small text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($booking['address']) ?>">
                                                            <?= htmlspecialchars($booking['address']) ?>
                                                        </div>
                                                    </td>
                                                    
                                                    <td>
                                                        <span class="fw-bold text-primary"><?= htmlspecialchars($booking['service_name']) ?></span><br>
                                                        <small class="text-danger fw-bold"><i class="fa-regular fa-clock"></i> <?= date('d/m H:i', strtotime($booking['appointment_time'])) ?></small>
                                                        <div class="small text-muted">Quận: <?= htmlspecialchars($booking['district']) ?></div>
                                                    </td>
                                                    
                                                    <td>
                                                        <?php 
                                                            $stt = $booking['status'];
                                                            $cls = 'bg-secondary';
                                                            if($stt=='pending') $cls='status-pending';
                                                            if($stt=='confirmed') $cls='status-confirmed';
                                                            if($stt=='completed') $cls='status-completed';
                                                        ?>
                                                        <span class="badge-status <?= $cls ?>"><?= ucfirst($stt) ?></span>
                                                    </td>

                                                    <td>
                                                        <?php if ($stt === 'pending' || $stt === 'confirmed'): ?>
                                                            <form class="assign-form" action="assign_technician.php" method="POST"> 
                                                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                                <select name="technician_id" required class="form-select form-select-sm">
                                                                    <option value="">-- Chọn --</option>
                                                                    <?php foreach ($tech_list as $tech): ?>
                                                                        <option value="<?= $tech['id'] ?>" <?= ($booking['technician_id'] == $tech['id']) ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($tech['name']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="submit" title="Lưu phân công"><i class="fa-solid fa-check"></i></button>
                                                            </form>
                                                        <?php else: ?>
                                                            <div class="text-success small fw-bold">
                                                                <i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($booking['tech_name'] ?? 'N/A') ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    
                                                    <td class="text-center">
                                                        <?php if ($stt === 'completed' || $stt === 'confirmed'): ?>
                                                            <a href="booking_invoice.php?id=<?= $booking['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success border-0" title="In hóa đơn">
                                                                <i class="fa-solid fa-print fa-lg"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy dữ liệu phù hợp.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if ($total_pages > 1): ?>
                        <div class="card-footer bg-white">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <li class="page-item <?= ($page <= 1 ? 'disabled' : '') ?>">
                                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search_term) ?>">«</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search_term) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?= ($page >= $total_pages ? 'disabled' : '') ?>">
                                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search_term) ?>">»</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card sticky-widget border-top border-3 border-primary">
                        <div class="card-header bg-white text-primary">
                            <i class="fa-solid fa-calendar-check"></i> Kiểm tra Lịch rảnh Thợ
                        </div>
                        <div class="card-body bg-light">
                            <label class="form-label fw-bold small text-muted">Chọn Kỹ thuật viên:</label>
                            <select id="tech_selector" class="form-select mb-3" onchange="loadTechSchedule(this.value)">
                                <option value="">-- Chọn thợ để xem --</option>
                                <?php foreach ($tech_list as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <div id="tech_schedule_result" class="schedule-box">
                                <div class="text-center text-muted py-5">
                                    <i class="fa-solid fa-magnifying-glass-chart fa-3x mb-3 opacity-25"></i>
                                    <p class="small">Vui lòng chọn thợ để xem lịch trình chi tiết.</p>
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
    // Hàm load lịch AJAX (Giữ nguyên)
    function loadTechSchedule(techId) {
        const container = document.getElementById('tech_schedule_result');
        if (!techId) {
            container.innerHTML = '<div class="text-center text-muted py-5"><p>Chọn thợ để xem lịch.</p></div>'; return;
        }
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        fetch('fetch_schedule.php?technician_id=' + techId)
            .then(res => res.text())
            .then(html => { container.innerHTML = html; })
            .catch(err => { container.innerHTML = '<p class="text-danger text-center">Lỗi tải dữ liệu.</p>'; });
    }
</script>
</body>
</html>
