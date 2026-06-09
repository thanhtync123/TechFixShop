<?php
session_start();
require_once '../../config/db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập.");
}

// --- PHẦN LOGIC TÍNH TOÁN (GIỮ NGUYÊN) ---
$sql = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month_str, SUM(final_price) as revenue
    FROM bookings 
    WHERE status IN ('completed', 'paid') AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month_str ORDER BY month_str ASC
";
$result = $conn->query($sql);
$months = []; $revenues = []; $x_values = []; $y_values = []; $i = 1;
while ($row = $result->fetch_assoc()) {
    $months[] = "Tháng " . date('m', strtotime($row['month_str']));
    $revenues[] = (float)$row['revenue'];
    $x_values[] = $i++; $y_values[] = (float)$row['revenue'];
}
// Fake data demo
if (count($revenues) < 2) {
    $months = ['T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
    $revenues = [15000000, 18000000, 16500000, 22000000, 25000000, 29000000];
    $x_values = [1, 2, 3, 4, 5, 6];
    $y_values = $revenues;
}
// Hồi quy tuyến tính
$n = count($x_values);
$sum_x = array_sum($x_values); $sum_y = array_sum($y_values);
$sum_xx = 0; $sum_xy = 0;
for ($k = 0; $k < $n; $k++) { $sum_xx += ($x_values[$k]**2); $sum_xy += ($x_values[$k]*$y_values[$k]); }
$den = ($n * $sum_xx) - ($sum_x**2);
if ($den == 0) { $a = 0; $b = 0; } else { $a = ($n*$sum_xy - $sum_x*$sum_y)/$den; $b = ($sum_y - $a*$sum_x)/$n; }
$predicted = ($a * ($n + 1)) + $b;
$trendline = []; for ($j = 1; $j <= $n + 1; $j++) { $trendline[] = ($a * $j) + $b; }
$growth = ($revenues[$n-1] > 0) ? (($predicted - $revenues[$n-1])/$revenues[$n-1])*100 : 0;
function fmt($n) { return number_format($n, 0, ',', '.') . ' đ'; }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dự Báo Doanh Thu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { background-color: #f4f6f9; }

        /* --- QUAN TRỌNG: CLASS ĐẨY NỘI DUNG SANG PHẢI --- */
        .main-push-right {
            margin-left: 250px; /* Đẩy sang phải 250px để tránh Sidebar */
            padding: 20px;
            min-height: 100vh;
            transition: margin-left 0.3s;
        }

        /* Mobile responsive: Nếu màn hình nhỏ thì bỏ margin (vì sidebar thường ẩn đi) */
        @media (max-width: 992px) {
            .main-push-right { margin-left: 0; }
        }

        .card-forecast {
            border: none; border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 25px; margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .forecast-amount { font-size: 2.5rem; font-weight: bold; margin: 10px 0; }
        .trend-badge { background: rgba(255,255,255,0.25); padding: 5px 15px; border-radius: 20px; }
        .chart-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); height: 100%; }
    </style>
</head>
<body>

            <?php include __DIR__ . '/template/sidebar.php'; ?>

    <div class="main-push-right">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <h3 class="fw-bold text-dark"><i class="fa-solid fa-wand-magic-sparkles text-primary"></i> Dự Báo Doanh Thu AI</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Quay lại Dashboard</a>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card-forecast">
                        <h6 class="text-uppercase opacity-75">Dự báo tháng tới (AI)</h6>
                        <div class="forecast-amount"><?= fmt($predicted) ?></div>
                        <div>
                            <?php if ($growth > 0): ?>
                                <span class="trend-badge"><i class="fa-solid fa-arrow-trend-up"></i> +<?= round($growth, 1) ?>%</span>
                            <?php else: ?>
                                <span class="trend-badge bg-danger"><i class="fa-solid fa-arrow-trend-down"></i> <?= round($growth, 1) ?>%</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold py-3">Chi tiết dữ liệu</div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th class="ps-3">Tháng</th><th class="text-end pe-3">Thực tế</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($months as $k => $m): ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?= $m ?></td>
                                        <td class="text-end pe-3 fw-bold"><?= fmt($revenues[$k]) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="chart-card">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('forecastChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_merge($months, ["Dự báo"])) ?>,
                datasets: [{
                    label: 'Thực tế',
                    data: <?= json_encode(array_merge($revenues, [null])) ?>,
                    borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3, pointRadius: 5, fill: true, tension: 0.3
                }, {
                    label: 'Xu hướng (AI)',
                    data: <?= json_encode($trendline) ?>,
                    borderColor: '#fd7e14', borderWidth: 2, borderDash: [5, 5],
                    pointRadius: 4, fill: false, tension: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>