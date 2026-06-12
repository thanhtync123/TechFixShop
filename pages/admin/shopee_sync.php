<?php
/**
 * Trang Quản Lý Đồng Bộ Shopee
 *
 * Chức năng:
 *  1. Nhập / lưu credentials Shopee (Partner ID, Key, Shop ID, Access Token)
 *  2. Lấy OAuth URL để authorize
 *  3. Xem danh sách services và equipments + trạng thái sync
 *  4. Đồng bộ 1 hoặc tất cả sản phẩm lên Shopee
 *  5. Cập nhật giá / tồn kho
 *  6. Xem log đồng bộ gần đây
 */

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /TechFixPHP/pages/public_page/login.php');
    exit;
}

require_once '../../config/db.php';
require_once '../../config/shopee.php';   // trả về array
require_once '../../libs/ShopeeAPI.php';

// ── Helper: lấy settings từ DB ──────────────────────────────
function getShopeeSettings(): array
{
    global $conn;
    $row = $conn->query("SELECT * FROM shopee_settings WHERE id = 1")->fetch_assoc();
    return $row ?? [];
}

function saveShopeeSettings(array $data): void
{
    global $conn;
    $stmt = $conn->prepare("
        UPDATE shopee_settings
           SET partner_id    = ?,
               shop_id       = ?,
               access_token  = ?,
               refresh_token = ?,
               token_expires = ?
         WHERE id = 1
    ");
    $stmt->bind_param(
        'iisss',
        $data['partner_id'],
        $data['shop_id'],
        $data['access_token'],
        $data['refresh_token'],
        $data['token_expires']
    );
    $stmt->execute();
}

// ── Helper: ghi log ──────────────────────────────────────────
function shopeeLog(string $type, int $srcId, string $action, array $req, array $res): void
{
    global $conn;
    $success = isset($res['error']) ? 0 : (($res['error'] ?? '') === '' ? 1 : 0);
    // Shopee trả lỗi qua trường "error"
    if (!empty($res['error'])) $success = 0;
    $stmt = $conn->prepare("
        INSERT INTO shopee_sync_log (source_type, source_id, action, request, response, success)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $r = json_encode($req,  JSON_UNESCAPED_UNICODE);
    $s = json_encode($res, JSON_UNESCAPED_UNICODE);
    $stmt->bind_param('sissi i', $type, $srcId, $action, $r, $s, $success);
    // fix bind type
    $stmt->bind_param('sisssi', $type, $srcId, $action, $r, $s, $success);
    $stmt->execute();
}

// ── Helper: upsert sync record ───────────────────────────────
function upsertSync(string $type, int $srcId, ?int $shopeeId, string $status, string $err = ''): void
{
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO shopee_product_sync
               (source_type, source_id, shopee_item_id, sync_status, last_synced_at, error_message)
        VALUES (?, ?, ?, ?, NOW(), ?)
        ON DUPLICATE KEY UPDATE
               shopee_item_id = VALUES(shopee_item_id),
               sync_status    = VALUES(sync_status),
               last_synced_at = NOW(),
               error_message  = VALUES(error_message)
    ");
    $stmt->bind_param('siiss', $type, $srcId, $shopeeId, $status, $err);
    $stmt->execute();
}

// ── Lấy cấu hình Shopee từ DB ────────────────────────────────
$dbSettings  = getShopeeSettings();
$shopeeConf  = require '../../config/shopee.php';

// Ưu tiên DB, nếu DB chưa có thì dùng config file
$partnerKey  = $shopeeConf['partner_key'];  // Key KHÔNG lưu DB
$partnerIdDb = !empty($dbSettings['partner_id']) ? (int)$dbSettings['partner_id'] : $shopeeConf['partner_id'];
$shopIdDb    = !empty($dbSettings['shop_id'])    ? (int)$dbSettings['shop_id']    : $shopeeConf['shop_id'];
$accessToken = !empty($dbSettings['access_token'])  ? $dbSettings['access_token']  : $shopeeConf['access_token'];
$refreshToken= $dbSettings['refresh_token'] ?? '';
$tokenExpiry = $dbSettings['token_expires'] ?? '';

$api = new ShopeeAPI([
    'partner_id'   => $partnerIdDb,
    'partner_key'  => $partnerKey,
    'shop_id'      => $shopIdDb,
    'access_token' => $accessToken,
    'env'          => $shopeeConf['env'],
    'base_url'     => $shopeeConf['base_url'],
]);

$msg      = '';
$msgType  = '';
$tab      = $_GET['tab'] ?? 'services';

// ════════════════════════════════════════════════════════════
//  XỬ LÝ ACTION
// ════════════════════════════════════════════════════════════

// ── 1. Lưu credentials ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_credentials'])) {
    $saveData = [
        'partner_id'   => (int)$_POST['partner_id'],
        'shop_id'      => (int)$_POST['shop_id'],
        'access_token' => trim($_POST['access_token']),
        'refresh_token'=> trim($_POST['refresh_token']),
        'token_expires'=> trim($_POST['token_expires']) ?: null,
    ];
    saveShopeeSettings($saveData);
    $msg     = 'Đã lưu thông tin xác thực Shopee thành công!';
    $msgType = 'success';
    // reload để dùng settings mới
    header("Location: shopee_sync.php?tab=settings&saved=1");
    exit;
}

// ── 2. Lấy Auth URL ─────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'get_auth_url') {
    $redirectUrl = 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST']
                 . '/TechFixPHP/pages/admin/shopee_callback.php';
    $authUrl = $api->getAuthUrl($redirectUrl);
    header("Location: $authUrl");
    exit;
}

// ── 3. Làm mới token ─────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'refresh_token') {
    if (empty($refreshToken)) {
        $msg = 'Chưa có Refresh Token. Hãy authorize lại.';
        $msgType = 'warning';
    } else {
        $res = $api->refreshToken($refreshToken, $shopIdDb);
        if (!empty($res['access_token'])) {
            $expires = date('Y-m-d H:i:s', time() + ($res['expire_in'] ?? 14400));
            saveShopeeSettings([
                'partner_id'   => $partnerIdDb,
                'shop_id'      => $shopIdDb,
                'access_token' => $res['access_token'],
                'refresh_token'=> $res['refresh_token'] ?? $refreshToken,
                'token_expires'=> $expires,
            ]);
            $msg = 'Làm mới Access Token thành công! Hết hạn: ' . $expires;
            $msgType = 'success';
        } else {
            $msg = 'Lỗi làm mới token: ' . json_encode($res, JSON_UNESCAPED_UNICODE);
            $msgType = 'danger';
        }
    }
}

// ── 4. Đồng bộ 1 sản phẩm ────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'sync_one') {
    $srcType = $_GET['type'] ?? 'service';
    $srcId   = (int)($_GET['id'] ?? 0);

    if ($srcId > 0) {
        // Lấy dữ liệu sản phẩm
        if ($srcType === 'service') {
            $row = $conn->query("SELECT * FROM services WHERE id = $srcId")->fetch_assoc();
            $categoryId = $shopeeConf['category_map'][$row['group_name'] ?? ''] ?? 100651;
            $item = [
                'name'        => $row['name'],
                'description' => $row['description'] ?? '',
                'price'       => (float)$row['price'],
                'stock'       => 999,
                'category_id' => $categoryId,
                'condition'   => 'NEW',
                'weight'      => 0.1,
                'image_ids'   => [],
                'logistic_info'=> [],
            ];
        } else {
            $row = $conn->query("SELECT * FROM equipments WHERE id = $srcId")->fetch_assoc();
            $item = [
                'name'        => $row['name'],
                'description' => $row['description'] ?? '',
                'price'       => (float)$row['price'],
                'stock'       => (int)$row['quantity'],
                'category_id' => 100651,
                'condition'   => 'NEW',
                'weight'      => 0.5,
                'image_ids'   => [],
                'logistic_info'=> [],
            ];
        }

        // Kiểm tra đã sync chưa
        $existSync = $conn->query("
            SELECT * FROM shopee_product_sync
             WHERE source_type = '$srcType' AND source_id = $srcId
        ")->fetch_assoc();

        if (!empty($existSync['shopee_item_id']) && $existSync['sync_status'] === 'synced') {
            // UPDATE
            $res = $api->updateItem((int)$existSync['shopee_item_id'], $item);
            $action = 'update_item';
        } else {
            // ADD NEW
            $res = $api->addItem($item);
            $action = 'add_item';
        }

        shopeeLog($srcType, $srcId, $action, $item, $res);

        if (empty($res['error']) && !empty($res['response']['item_id'])) {
            $newShopeeId = (int)$res['response']['item_id'];
            upsertSync($srcType, $srcId, $newShopeeId, 'synced');
            $msg = "✅ Đồng bộ thành công! Shopee Item ID: $newShopeeId";
            $msgType = 'success';
        } else {
            $errMsg = $res['message'] ?? $res['error'] ?? json_encode($res);
            upsertSync($srcType, $srcId, null, 'error', $errMsg);
            $msg = '❌ Lỗi đồng bộ: ' . htmlspecialchars($errMsg);
            $msgType = 'danger';
        }
    }
}

// ── 5. Đồng bộ tất cả ────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'sync_all') {
    $srcType  = $_GET['type'] ?? 'service';
    $table    = $srcType === 'service' ? 'services' : 'equipments';
    $rows     = [];
    $qr       = $conn->query("SELECT id FROM $table");
    while ($r = $qr->fetch_assoc()) $rows[] = (int)$r['id'];

    $successCount = 0;
    $failCount    = 0;

    foreach ($rows as $srcId) {
        $redirectUrl = "shopee_sync.php?action=sync_one&type=$srcType&id=$srcId&_bulk=1";
        // Gọi trực tiếp thay vì redirect
        $row = $conn->query("SELECT * FROM $table WHERE id = $srcId")->fetch_assoc();

        if ($srcType === 'service') {
            $categoryId = $shopeeConf['category_map'][$row['group_name'] ?? ''] ?? 100651;
            $item = [
                'name'        => $row['name'],
                'description' => $row['description'] ?? '',
                'price'       => (float)$row['price'],
                'stock'       => 999,
                'category_id' => $categoryId,
                'condition'   => 'NEW',
                'weight'      => 0.1,
                'image_ids'   => [],
                'logistic_info'=> [],
            ];
        } else {
            $item = [
                'name'        => $row['name'],
                'description' => $row['description'] ?? '',
                'price'       => (float)$row['price'],
                'stock'       => (int)$row['quantity'],
                'category_id' => 100651,
                'condition'   => 'NEW',
                'weight'      => 0.5,
                'image_ids'   => [],
                'logistic_info'=> [],
            ];
        }

        $existSync = $conn->query("
            SELECT * FROM shopee_product_sync
             WHERE source_type = '$srcType' AND source_id = $srcId
        ")->fetch_assoc();

        if (!empty($existSync['shopee_item_id']) && $existSync['sync_status'] === 'synced') {
            $res    = $api->updateItem((int)$existSync['shopee_item_id'], $item);
            $action = 'update_item';
        } else {
            $res    = $api->addItem($item);
            $action = 'add_item';
        }

        shopeeLog($srcType, $srcId, $action, $item, $res);

        if (empty($res['error']) && !empty($res['response']['item_id'])) {
            upsertSync($srcType, $srcId, (int)$res['response']['item_id'], 'synced');
            $successCount++;
        } else {
            $errMsg = $res['message'] ?? $res['error'] ?? json_encode($res);
            upsertSync($srcType, $srcId, null, 'error', $errMsg);
            $failCount++;
        }
    }

    $msg = "Đồng bộ hoàn tất: ✅ $successCount thành công, ❌ $failCount lỗi";
    $msgType = ($failCount === 0) ? 'success' : 'warning';
}

// ── 6. Cập nhật giá ──────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'update_price') {
    $srcType   = $_GET['type'] ?? 'service';
    $srcId     = (int)($_GET['id'] ?? 0);
    $syncRow   = $conn->query("
        SELECT * FROM shopee_product_sync
         WHERE source_type = '$srcType' AND source_id = $srcId AND sync_status = 'synced'
    ")->fetch_assoc();

    if (!empty($syncRow['shopee_item_id'])) {
        $table   = $srcType === 'service' ? 'services' : 'equipments';
        $prodRow = $conn->query("SELECT price FROM $table WHERE id = $srcId")->fetch_assoc();
        $res     = $api->updatePrice((int)$syncRow['shopee_item_id'], (float)$prodRow['price']);
        shopeeLog($srcType, $srcId, 'update_price', ['price' => $prodRow['price']], $res);

        if (empty($res['error'])) {
            $msg = '✅ Cập nhật giá thành công!';
            $msgType = 'success';
        } else {
            $msg = '❌ Lỗi: ' . htmlspecialchars($res['message'] ?? $res['error']);
            $msgType = 'danger';
        }
    } else {
        $msg = 'Sản phẩm này chưa được đồng bộ lên Shopee.';
        $msgType = 'warning';
    }
}

// ── 7. Cập nhật tồn kho ──────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'update_stock') {
    $srcId   = (int)($_GET['id'] ?? 0);
    $syncRow = $conn->query("
        SELECT * FROM shopee_product_sync
         WHERE source_type = 'equipment' AND source_id = $srcId AND sync_status = 'synced'
    ")->fetch_assoc();

    if (!empty($syncRow['shopee_item_id'])) {
        $eRow = $conn->query("SELECT quantity FROM equipments WHERE id = $srcId")->fetch_assoc();
        $res  = $api->updateStock((int)$syncRow['shopee_item_id'], (int)$eRow['quantity']);
        shopeeLog('equipment', $srcId, 'update_stock', ['quantity' => $eRow['quantity']], $res);

        if (empty($res['error'])) {
            $msg = '✅ Cập nhật tồn kho thành công!';
            $msgType = 'success';
        } else {
            $msg = '❌ Lỗi: ' . htmlspecialchars($res['message'] ?? $res['error']);
            $msgType = 'danger';
        }
    } else {
        $msg = 'Thiết bị này chưa được đồng bộ lên Shopee.';
        $msgType = 'warning';
    }
}

// ── Flash message từ redirect ─────────────────────────────────
if (isset($_GET['saved'])) {
    $msg = 'Đã lưu thông tin xác thực Shopee thành công!';
    $msgType = 'success';
}

// ── Lấy dữ liệu hiển thị ─────────────────────────────────────
$services   = [];
$equipments = [];
$syncMap    = [];

$qr = $conn->query("SELECT * FROM shopee_product_sync");
while ($r = $qr->fetch_assoc()) {
    $syncMap[$r['source_type'] . '_' . $r['source_id']] = $r;
}

$qr = $conn->query("SELECT * FROM services ORDER BY group_name, id");
while ($r = $qr->fetch_assoc()) $services[] = $r;

$qr = $conn->query("SELECT * FROM equipments ORDER BY id");
while ($r = $qr->fetch_assoc()) $equipments[] = $r;

// Log gần đây
$recentLogs = [];
$qr = $conn->query("SELECT * FROM shopee_sync_log ORDER BY created_at DESC LIMIT 50");
while ($r = $qr->fetch_assoc()) $recentLogs[] = $r;

// Thống kê
$stats = [
    'total_services'   => count($services),
    'total_equipment'  => count($equipments),
    'synced'           => 0,
    'pending'          => 0,
    'error'            => 0,
];
foreach ($syncMap as $s) {
    if ($s['sync_status'] === 'synced')  $stats['synced']++;
    if ($s['sync_status'] === 'pending') $stats['pending']++;
    if ($s['sync_status'] === 'error')   $stats['error']++;
}

// reload settings sau khi save
$dbSettings = getShopeeSettings();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đồng Bộ Shopee – TechFix Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body { background: #f1f5f9; }
        .shopee-header { background: linear-gradient(135deg, #ee4d2d 0%, #f8a500 100%); color: #fff; }
        .badge-synced  { background-color: #22c55e; }
        .badge-pending { background-color: #f59e0b; }
        .badge-error   { background-color: #ef4444; }
        .badge-none    { background-color: #94a3b8; }
        .shopee-logo   { font-size: 2rem; }
        .stat-card     { border-left: 4px solid; }
        .stat-synced   { border-color: #22c55e; }
        .stat-error    { border-color: #ef4444; }
        .stat-total    { border-color: #3b82f6; }
        code.small     { font-size: .78rem; word-break: break-all; }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block collapse p-0">
        <?php include __DIR__ . '/template/sidebar.php'; ?>
    </nav>

    <!-- Main -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">

        <!-- Header -->
        <div class="shopee-header rounded-3 p-4 mb-4 d-flex align-items-center gap-3">
            <span class="shopee-logo">🛒</span>
            <div>
                <h2 class="mb-0 fw-bold">Đồng Bộ Shopee</h2>
                <small>Đẩy dịch vụ & thiết bị từ TechFix lên Shopee Seller</small>
            </div>
        </div>

        <!-- Alert -->
        <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-total p-3">
                    <div class="text-muted small">Tổng dịch vụ</div>
                    <div class="fs-3 fw-bold text-primary"><?= $stats['total_services'] ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-total p-3">
                    <div class="text-muted small">Tổng thiết bị</div>
                    <div class="fs-3 fw-bold text-primary"><?= $stats['total_equipment'] ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-synced p-3">
                    <div class="text-muted small">Đã đồng bộ</div>
                    <div class="fs-3 fw-bold text-success"><?= $stats['synced'] ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card stat-card stat-error p-3">
                    <div class="text-muted small">Lỗi</div>
                    <div class="fs-3 fw-bold text-danger"><?= $stats['error'] ?></div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="mainTab">
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'services' ? 'active' : '' ?>" href="?tab=services">
                    <i class="fa-solid fa-list-check"></i> Dịch Vụ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'equipment' ? 'active' : '' ?>" href="?tab=equipment">
                    <i class="fa-solid fa-box-open"></i> Thiết Bị
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'logs' ? 'active' : '' ?>" href="?tab=logs">
                    <i class="fa-solid fa-clock-rotate-left"></i> Lịch Sử
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'settings' ? 'active' : '' ?>" href="?tab=settings">
                    <i class="fa-solid fa-key"></i> Cấu Hình API
                </a>
            </li>
        </ul>

        <!-- ── TAB: SERVICES ──────────────────────────────────── -->
        <?php if ($tab === 'services'): ?>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-list-check text-primary"></i> Danh Sách Dịch Vụ</span>
                <a href="?action=sync_all&type=service&tab=services"
                   class="btn btn-sm btn-warning"
                   onclick="return confirm('Đồng bộ TẤT CẢ dịch vụ lên Shopee?')">
                    <i class="fa-solid fa-rotate"></i> Đồng Bộ Tất Cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="tblServices">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên Dịch Vụ</th>
                                <th>Nhóm</th>
                                <th>Giá (VNĐ)</th>
                                <th>Trạng Thái Sync</th>
                                <th>Shopee Item ID</th>
                                <th>Lần Sync Cuối</th>
                                <th class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($services as $svc): 
                            $key    = 'service_' . $svc['id'];
                            $sync   = $syncMap[$key] ?? null;
                            $status = $sync['sync_status'] ?? 'none';
                            $shopeeId = $sync['shopee_item_id'] ?? '—';
                            $lastSync = $sync['last_synced_at'] ?? '—';
                        ?>
                        <tr>
                            <td><?= $svc['id'] ?></td>
                            <td>
                                <?php if (!empty($svc['image'])): ?>
                                    <img src="<?= htmlspecialchars($svc['image']) ?>"
                                         width="36" height="36"
                                         class="rounded me-2 object-fit-cover"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                                <?= htmlspecialchars($svc['name']) ?>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($svc['group_name'] ?? '—') ?></span></td>
                            <td><?= number_format($svc['price']) ?></td>
                            <td>
                                <?php if ($status === 'synced'): ?>
                                    <span class="badge badge-synced">✅ Đã Sync</span>
                                <?php elseif ($status === 'error'): ?>
                                    <span class="badge badge-error" title="<?= htmlspecialchars($sync['error_message'] ?? '') ?>">❌ Lỗi</span>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="badge badge-pending">⏳ Đang chờ</span>
                                <?php else: ?>
                                    <span class="badge badge-none">— Chưa sync</span>
                                <?php endif; ?>
                            </td>
                            <td><code class="small"><?= $shopeeId ?></code></td>
                            <td><small class="text-muted"><?= $lastSync ?></small></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="?action=sync_one&type=service&id=<?= $svc['id'] ?>&tab=services"
                                       class="btn btn-outline-warning"
                                       title="<?= $status === 'synced' ? 'Cập nhật' : 'Đồng bộ mới' ?>">
                                        <i class="fa-solid fa-<?= $status === 'synced' ? 'rotate' : 'upload' ?>"></i>
                                        <?= $status === 'synced' ? 'Update' : 'Sync' ?>
                                    </a>
                                    <?php if ($status === 'synced'): ?>
                                    <a href="?action=update_price&type=service&id=<?= $svc['id'] ?>&tab=services"
                                       class="btn btn-outline-info" title="Cập nhật giá">
                                        <i class="fa-solid fa-tag"></i> Giá
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── TAB: EQUIPMENT ────────────────────────────────── -->
        <?php elseif ($tab === 'equipment'): ?>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-box-open text-success"></i> Kho Thiết Bị</span>
                <a href="?action=sync_all&type=equipment&tab=equipment"
                   class="btn btn-sm btn-warning"
                   onclick="return confirm('Đồng bộ TẤT CẢ thiết bị lên Shopee?')">
                    <i class="fa-solid fa-rotate"></i> Đồng Bộ Tất Cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="tblEquip">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên Thiết Bị</th>
                                <th>Đơn Vị</th>
                                <th>Giá</th>
                                <th>Tồn Kho</th>
                                <th>Trạng Thái Sync</th>
                                <th>Shopee Item ID</th>
                                <th class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($equipments as $eq):
                            $key      = 'equipment_' . $eq['id'];
                            $sync     = $syncMap[$key] ?? null;
                            $status   = $sync['sync_status'] ?? 'none';
                            $shopeeId = $sync['shopee_item_id'] ?? '—';
                        ?>
                        <tr>
                            <td><?= $eq['id'] ?></td>
                            <td>
                                <?php if (!empty($eq['img'])): ?>
                                    <img src="/TechFixPHP/assets/image/<?= htmlspecialchars($eq['img']) ?>"
                                         width="36" height="36"
                                         class="rounded me-2 object-fit-cover"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                                <?= htmlspecialchars($eq['name']) ?>
                            </td>
                            <td><?= htmlspecialchars($eq['unit'] ?? '—') ?></td>
                            <td><?= number_format($eq['price']) ?></td>
                            <td>
                                <span class="badge <?= ($eq['quantity'] ?? 0) > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $eq['quantity'] ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($status === 'synced'): ?>
                                    <span class="badge badge-synced">✅ Đã Sync</span>
                                <?php elseif ($status === 'error'): ?>
                                    <span class="badge badge-error" title="<?= htmlspecialchars($sync['error_message'] ?? '') ?>">❌ Lỗi</span>
                                <?php else: ?>
                                    <span class="badge badge-none">— Chưa sync</span>
                                <?php endif; ?>
                            </td>
                            <td><code class="small"><?= $shopeeId ?></code></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="?action=sync_one&type=equipment&id=<?= $eq['id'] ?>&tab=equipment"
                                       class="btn btn-outline-warning">
                                        <i class="fa-solid fa-<?= $status === 'synced' ? 'rotate' : 'upload' ?>"></i>
                                        <?= $status === 'synced' ? 'Update' : 'Sync' ?>
                                    </a>
                                    <?php if ($status === 'synced'): ?>
                                    <a href="?action=update_price&type=equipment&id=<?= $eq['id'] ?>&tab=equipment"
                                       class="btn btn-outline-info" title="Cập nhật giá">
                                        <i class="fa-solid fa-tag"></i>
                                    </a>
                                    <a href="?action=update_stock&id=<?= $eq['id'] ?>&tab=equipment"
                                       class="btn btn-outline-secondary" title="Sync tồn kho">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── TAB: LOGS ─────────────────────────────────────── -->
        <?php elseif ($tab === 'logs'): ?>
        <div class="card shadow-sm">
            <div class="card-header"><i class="fa-solid fa-clock-rotate-left text-secondary"></i> Lịch Sử Đồng Bộ (50 gần nhất)</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="tblLogs">
                        <thead class="table-light">
                            <tr>
                                <th>Thời Gian</th>
                                <th>Nguồn</th>
                                <th>ID</th>
                                <th>Hành Động</th>
                                <th>Kết Quả</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><small><?= $log['created_at'] ?></small></td>
                            <td>
                                <span class="badge <?= $log['source_type'] === 'service' ? 'bg-primary' : 'bg-success' ?>">
                                    <?= $log['source_type'] ?>
                                </span>
                            </td>
                            <td><?= $log['source_id'] ?></td>
                            <td><code class="small"><?= htmlspecialchars($log['action']) ?></code></td>
                            <td>
                                <?php if ($log['success']): ?>
                                    <span class="badge bg-success">✅ OK</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ Lỗi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-outline-secondary btn-sm"
                                        onclick="showResponse(<?= htmlspecialchars(json_encode($log['response']), ENT_QUOTES) ?>)">
                                    Xem
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có lịch sử đồng bộ</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── TAB: SETTINGS ─────────────────────────────────── -->
        <?php elseif ($tab === 'settings'): ?>
        <div class="row g-3">
            <!-- Form credentials -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header"><i class="fa-solid fa-key text-warning"></i> Thông Tin Xác Thực Shopee API</div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Cách lấy thông tin:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Truy cập <a href="https://open.shopee.com" target="_blank">open.shopee.com</a> → Đăng nhập với tài khoản Shopee Partner</li>
                                <li>Vào <strong>My Apps</strong> → Chọn app của bạn</li>
                                <li>Lấy <strong>Partner ID</strong> và <strong>Partner Key</strong></li>
                                <li>Nhập <strong>Shop ID</strong> từ Shopee Seller Center → Cài đặt tài khoản</li>
                                <li>Điền vào form dưới → Nhấn <strong>Lấy Auth URL</strong> để lấy Access Token</li>
                            </ol>
                        </div>

                        <div class="alert alert-warning">
                            ⚠️ <strong>Partner Key</strong> phải điền trong file <code>config/shopee.php</code> (không lưu DB vì lý do bảo mật).
                        </div>

                        <form method="POST" action="?tab=settings">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Partner ID</label>
                                <input type="number" name="partner_id" class="form-control"
                                       value="<?= (int)($dbSettings['partner_id'] ?? 0) ?>"
                                       placeholder="VD: 1234567">
                                <div class="form-text">Từ trang quản lý app trên Shopee Open Platform</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Shop ID</label>
                                <input type="number" name="shop_id" class="form-control"
                                       value="<?= (int)($dbSettings['shop_id'] ?? 0) ?>"
                                       placeholder="VD: 987654321">
                                <div class="form-text">ID cửa hàng Shopee của bạn</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Access Token</label>
                                <input type="text" name="access_token" class="form-control font-monospace"
                                       value="<?= htmlspecialchars($dbSettings['access_token'] ?? '') ?>"
                                       placeholder="Sau khi authorize sẽ có token này">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Refresh Token</label>
                                <input type="text" name="refresh_token" class="form-control font-monospace"
                                       value="<?= htmlspecialchars($dbSettings['refresh_token'] ?? '') ?>"
                                       placeholder="Dùng để tự động gia hạn access token">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Token Expires At</label>
                                <input type="text" name="token_expires" class="form-control"
                                       value="<?= htmlspecialchars($dbSettings['token_expires'] ?? '') ?>"
                                       placeholder="VD: 2026-07-12 10:00:00">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="save_credentials" class="btn btn-primary">
                                    <i class="fa-solid fa-save"></i> Lưu Credentials
                                </button>
                                <a href="?action=refresh_token&tab=settings" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-rotate"></i> Làm Mới Token
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn OAuth -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark"><i class="fa-brands fa-shopee"></i> Lấy Access Token (OAuth)</div>
                    <div class="card-body">
                        <p>Nhấn nút dưới để mở trang đăng nhập Shopee. Sau khi authorize, hệ thống sẽ tự điền Access Token.</p>

                        <?php if (empty($partnerKey) || $partnerIdDb === 0): ?>
                        <div class="alert alert-danger">
                            ❌ Chưa cấu hình <code>partner_key</code> trong <code>config/shopee.php</code>
                        </div>
                        <?php else: ?>
                        <a href="?action=get_auth_url" class="btn btn-warning w-100 mb-2">
                            <i class="fa-brands fa-shopee"></i> Mở Trang Shopee OAuth →
                        </a>
                        <?php endif; ?>

                        <hr>
                        <h6 class="fw-semibold">Trạng Thái Hiện Tại</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Partner ID:</td>
                                <td><strong><?= $partnerIdDb ?: '—' ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Shop ID:</td>
                                <td><strong><?= $shopIdDb ?: '—' ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Access Token:</td>
                                <td>
                                    <?php if (!empty($accessToken)): ?>
                                        <span class="badge bg-success">✅ Đã có</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">❌ Chưa có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Hết hạn:</td>
                                <td>
                                    <?php
                                    if ($tokenExpiry) {
                                        $expired = strtotime($tokenExpiry) < time();
                                        echo '<span class="badge ' . ($expired ? 'bg-danger' : 'bg-success') . '">'
                                           . htmlspecialchars($tokenExpiry) . '</span>';
                                    } else {
                                        echo '<span class="text-muted">—</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Môi trường:</td>
                                <td>
                                    <span class="badge <?= $shopeeConf['env'] === 'production' ? 'bg-danger' : 'bg-info' ?>">
                                        <?= strtoupper($shopeeConf['env']) ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header"><i class="fa-solid fa-circle-info"></i> Ghi Chú</div>
                    <div class="card-body small text-muted">
                        <ul class="mb-0">
                            <li>Môi trường <strong>sandbox</strong>: dùng để test, không ảnh hưởng shop thật</li>
                            <li>Đổi sang <strong>production</strong> trong <code>config/shopee.php</code> khi muốn đẩy lên thật</li>
                            <li>Access token mặc định hết hạn sau <strong>4 giờ</strong>, dùng Refresh Token để gia hạn</li>
                            <li>Sản phẩm mới tạo ở trạng thái <strong>UNLIST</strong> — cần vào Shopee Seller Center duyệt</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>
</div>

<!-- Modal xem response -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Response từ Shopee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="responseBody" class="bg-dark text-light p-3 rounded" style="max-height:400px;overflow:auto;font-size:.85rem;"></pre>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#tblServices, #tblEquip').DataTable({
        language: { url: '/TechFixPHP/assets/js/datatable-vn.js' },
        pageLength: 25,
        order: [[0, 'asc']],
    });
    $('#tblLogs').DataTable({
        language: { url: '/TechFixPHP/assets/js/datatable-vn.js' },
        pageLength: 25,
        order: [[0, 'desc']],
    });
});

function showResponse(jsonStr) {
    try {
        const obj = JSON.parse(jsonStr);
        document.getElementById('responseBody').textContent = JSON.stringify(obj, null, 2);
    } catch (e) {
        document.getElementById('responseBody').textContent = jsonStr;
    }
    new bootstrap.Modal(document.getElementById('responseModal')).show();
}
</script>
</body>
</html>
