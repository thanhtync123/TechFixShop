<?php
session_start();
require_once '../../config/db.php';

$serviceId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function tableHasColumn(mysqli $conn, string $table, string $column): bool
{
    $sql = "SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $exists = !empty($row['total']);
    $stmt->close();

    return $exists;
}

function serviceImage(array $service): string
{
    if (!empty($service['image'])) {
        return $service['image'];
    }

    $name = mb_strtolower($service['name'] ?? '', 'UTF-8');
    $map = [
        'máy tính' => '/TechFixPHP/assets/image/computer.jpg',
        'laptop' => '/TechFixPHP/assets/image/laptop.jpg',
        'pc' => '/TechFixPHP/assets/image/pc.jpg',
        'máy lạnh' => '/TechFixPHP/assets/image/air.jpg',
        'camera' => '/TechFixPHP/assets/image/camera.jpg',
        'ống nước' => '/TechFixPHP/assets/image/plumbing.jpg',
        'nước' => '/TechFixPHP/assets/image/waterpump.jpg',
        'điện' => '/TechFixPHP/assets/image/elec.jpg',
        'tivi' => '/TechFixPHP/assets/image/appliances.jpg',
        'máy giặt' => '/TechFixPHP/assets/image/washingmachine.jpg',
        'wifi' => '/TechFixPHP/assets/image/network.jpg',
    ];

    foreach ($map as $keyword => $image) {
        if (mb_strpos($name, $keyword) !== false) {
            return $image;
        }
    }

    return '/TechFixPHP/assets/image/tools.jpg';
}

$hasImage = tableHasColumn($conn, 'services', 'image');
$hasGroup = tableHasColumn($conn, 'services', 'group_name');
$selectFields = 'id, name, description, price, unit';
$selectFields .= $hasImage ? ', image' : ', NULL AS image';
$selectFields .= $hasGroup ? ', group_name' : ', NULL AS group_name';

$service = null;
$relatedServices = [];

if ($serviceId > 0) {
    $stmt = $conn->prepare("SELECT $selectFields FROM services WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $serviceId);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($service) {
    $relatedSql = "SELECT $selectFields FROM services WHERE id <> ?";
    $types = 'i';
    $params = [$serviceId];

    if (!empty($service['group_name']) && $hasGroup) {
        $relatedSql .= " AND group_name = ?";
        $types .= 's';
        $params[] = $service['group_name'];
    }

    $relatedSql .= " ORDER BY id ASC LIMIT 3";
    $stmt = $conn->prepare($relatedSql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $relatedServices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$pageTitle = $service ? $service['name'] . ' - TECHFIX' : 'Không tìm thấy dịch vụ - TECHFIX';
$description = trim($service['description'] ?? '');
$price = isset($service['price']) ? (float) $service['price'] : 0;
$unit = trim($service['unit'] ?? '');
$image = $service ? serviceImage($service) : '/TechFixPHP/assets/image/tools.jpg';
$bookUrl = '/TechFixPHP/Customer/book.php' . ($service ? '?service_id=' . (int) $service['id'] : '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: #1f2937;
            background: #f4f7fb;
            line-height: 1.6;
        }
        a { color: inherit; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px min(6vw, 72px);
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: #0f3f7a;
            text-decoration: none;
            letter-spacing: 0;
        }
        .brand img { width: 38px; height: 46px; object-fit: contain; }
        .nav-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
        .nav-actions a {
            text-decoration: none;
            font-weight: 700;
            color: #374151;
            padding: 9px 13px;
            border-radius: 8px;
        }
        .nav-actions a:hover { background: #eef5ff; color: #0f3f7a; }
        .hero {
            min-height: 420px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr);
            align-items: stretch;
            background: #0f3f7a;
        }
        .hero-copy {
            padding: clamp(34px, 7vw, 86px) min(6vw, 72px);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .eyebrow {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.13);
            font-size: .9rem;
            font-weight: 700;
            margin-bottom: 18px;
        }
        h1 {
            font-size: clamp(2rem, 5vw, 4rem);
            line-height: 1.05;
            margin: 0 0 18px;
            letter-spacing: 0;
        }
        .hero-copy p {
            max-width: 680px;
            margin: 0 0 28px;
            color: #dbeafe;
            font-size: 1.08rem;
        }
        .hero-image { min-height: 360px; background: #dbeafe; }
        .hero-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }
        .button-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 800;
            border: 1px solid transparent;
        }
        .btn-primary { background: #f8fafc; color: #0f3f7a; }
        .btn-secondary { color: #fff; border-color: rgba(255,255,255,.4); }
        .btn-secondary:hover { background: rgba(255,255,255,.1); }
        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 34px 20px 56px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 24px;
            align-items: start;
        }
        .panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(15, 63, 122, .08);
        }
        .panel h2, .panel h3 { margin: 0 0 14px; color: #0f3f7a; letter-spacing: 0; }
        .description {
            white-space: pre-line;
            color: #4b5563;
            margin: 0;
        }
        .steps {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }
        .step {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            align-items: start;
        }
        .step span {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e8f2ff;
            color: #0f3f7a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }
        .step strong { display: block; margin-bottom: 3px; }
        .side-card { position: sticky; top: 92px; }
        .price {
            font-size: 2rem;
            font-weight: 900;
            color: #0f766e;
            margin: 8px 0 4px;
        }
        .muted { color: #6b7280; }
        .info-list {
            display: grid;
            gap: 12px;
            padding: 18px 0;
            margin: 18px 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-list div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }
        .info-list strong { color: #111827; text-align: right; }
        .full-btn {
            width: 100%;
            background: #0f3f7a;
            color: #fff;
            margin-bottom: 10px;
        }
        .outline-btn {
            width: 100%;
            border-color: #d1d5db;
            color: #374151;
            background: #fff;
        }
        .related { margin-top: 28px; }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .related-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(15, 63, 122, .07);
        }
        .related-card img {
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            display: block;
        }
        .related-card div { padding: 14px; }
        .related-card h3 { margin: 0 0 8px; font-size: 1.02rem; color: #0f3f7a; }
        .not-found {
            min-height: 70vh;
            display: grid;
            place-items: center;
            padding: 30px 18px;
        }
        .not-found .panel { max-width: 620px; text-align: center; }
        @media (max-width: 900px) {
            .hero, .detail-grid { grid-template-columns: 1fr; }
            .hero-image { min-height: 260px; order: -1; }
            .side-card { position: static; }
            .related-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .nav-actions { justify-content: flex-start; }
            .panel { padding: 18px; }
        }
    </style>
</head>
<body>
    <nav class="topbar">
        <a class="brand" href="/TechFixPHP/index.php">
            <img src="/TechFixPHP/assets/image/VLUTE.png" alt="TECHFIX">
            <span>TECHFIX</span>
        </a>
        <div class="nav-actions">
            <a href="/TechFixPHP/index.php">Trang chủ</a>
            <a href="/TechFixPHP/Customer/Service.php">Dịch vụ</a>
            <a href="/TechFixPHP/Customer/my_booking.php">Lịch của tôi</a>
        </div>
    </nav>

    <?php if (!$service): ?>
        <main class="not-found">
            <section class="panel">
                <h1>Không tìm thấy dịch vụ</h1>
                <p class="muted">Dịch vụ bạn đang mở không tồn tại hoặc đã được gỡ khỏi hệ thống.</p>
                <div class="button-row" style="justify-content:center;">
                    <a class="btn btn-primary" href="/TechFixPHP/Customer/Service.php">
                        <i class="fa-solid fa-list"></i> Xem dịch vụ
                    </a>
                    <a class="btn outline-btn" style="width:auto;" href="/TechFixPHP/index.php">
                        <i class="fa-solid fa-house"></i> Trang chủ
                    </a>
                </div>
            </section>
        </main>
    <?php else: ?>
        <header class="hero">
            <div class="hero-copy">
                <div class="eyebrow"><i class="fa-solid fa-screwdriver-wrench"></i> Dịch vụ TECHFIX</div>
                <h1><?= e($service['name']) ?></h1>
                <p><?= e($description ?: 'Dịch vụ sửa chữa, lắp đặt và bảo trì được thực hiện bởi đội ngũ kỹ thuật TECHFIX.') ?></p>
                <div class="button-row">
                    <a class="btn btn-primary" href="<?= e($bookUrl) ?>">
                        <i class="fa-solid fa-calendar-check"></i> Đặt dịch vụ
                    </a>
                    <a class="btn btn-secondary" href="/TechFixPHP/Customer/Service.php">
                        <i class="fa-solid fa-arrow-left"></i> Dịch vụ khác
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?= e($image) ?>" alt="<?= e($service['name']) ?>" onerror="this.src='/TechFixPHP/assets/image/tools.jpg'">
            </div>
        </header>

        <main class="container">
            <div class="detail-grid">
                <section class="panel">
                    <h2>Chi tiết dịch vụ</h2>
                    <p class="description"><?= e($description ?: 'TECHFIX sẽ tiếp nhận yêu cầu, kiểm tra tình trạng thực tế và tư vấn phương án xử lý phù hợp trước khi thực hiện.') ?></p>

                    <div class="steps">
                        <div class="step">
                            <span>1</span>
                            <div>
                                <strong>Tiếp nhận yêu cầu</strong>
                                <p class="muted">Khách hàng chọn dịch vụ, khu vực và thời gian mong muốn.</p>
                            </div>
                        </div>
                        <div class="step">
                            <span>2</span>
                            <div>
                                <strong>Kiểm tra và báo giá</strong>
                                <p class="muted">Kỹ thuật viên xác nhận tình trạng, chi phí dự kiến và vật tư nếu có.</p>
                            </div>
                        </div>
                        <div class="step">
                            <span>3</span>
                            <div>
                                <strong>Thực hiện và nghiệm thu</strong>
                                <p class="muted">Hoàn tất xử lý, hướng dẫn sử dụng/bảo trì và cập nhật trạng thái đơn.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="panel side-card">
                    <h3>Thông tin nhanh</h3>
                    <div class="price"><?= number_format($price, 0, ',', '.') ?> đ</div>
                    <div class="muted">Giá tham khảo<?= $unit ? ' / ' . e($unit) : '' ?></div>

                    <div class="info-list">
                        <div>
                            <span>Mã dịch vụ</span>
                            <strong>#<?= (int) $service['id'] ?></strong>
                        </div>
                        <div>
                            <span>Đơn vị tính</span>
                            <strong><?= e($unit ?: 'Theo yêu cầu') ?></strong>
                        </div>
                        <div>
                            <span>Nhóm</span>
                            <strong><?= e($service['group_name'] ?: 'Dịch vụ chung') ?></strong>
                        </div>
                    </div>

                    <a class="btn full-btn" href="<?= e($bookUrl) ?>">
                        <i class="fa-solid fa-calendar-plus"></i> Đặt lịch ngay
                    </a>
                    <a class="btn outline-btn" href="/TechFixPHP/pages/public_page/chatbot.php">
                        <i class="fa-solid fa-comments"></i> Tư vấn thêm
                    </a>
                </aside>
            </div>

            <?php if (!empty($relatedServices)): ?>
                <section class="related">
                    <h2>Dịch vụ liên quan</h2>
                    <div class="related-grid">
                        <?php foreach ($relatedServices as $related): ?>
                            <a class="related-card" href="/TechFixPHP/pages/public_page/service_detail.php?id=<?= (int) $related['id'] ?>">
                                <img src="<?= e(serviceImage($related)) ?>" alt="<?= e($related['name']) ?>" onerror="this.src='/TechFixPHP/assets/image/tools.jpg'">
                                <div>
                                    <h3><?= e($related['name']) ?></h3>
                                    <p class="muted"><?= number_format((float) $related['price'], 0, ',', '.') ?> đ<?= !empty($related['unit']) ? ' / ' . e($related['unit']) : '' ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    <?php endif; ?>
</body>
</html>
