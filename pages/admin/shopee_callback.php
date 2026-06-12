<?php
/**
 * Shopee OAuth Callback
 *
 * Shopee sẽ redirect về URL này sau khi user authorize, kèm theo:
 *   ?code=xxx&shop_id=xxx
 *
 * Script này đổi code lấy access_token và lưu vào DB.
 */

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /TechFixPHP/pages/public_page/login.php');
    exit;
}

require_once '../../config/db.php';
require_once '../../libs/ShopeeAPI.php';

$shopeeConf = require '../../config/shopee.php';

$code   = trim($_GET['code']   ?? '');
$shopId = (int)($_GET['shop_id'] ?? 0);

if (empty($code) || $shopId === 0) {
    $err = urlencode('Shopee không trả về code hoặc shop_id hợp lệ. Thử lại.');
    header("Location: /TechFixPHP/pages/admin/shopee_sync.php?tab=settings&error=$err");
    exit;
}

// Lấy partner_id đã lưu trong DB (hoặc config)
$row       = $conn->query("SELECT partner_id FROM shopee_settings WHERE id = 1")->fetch_assoc();
$partnerId = !empty($row['partner_id']) ? (int)$row['partner_id'] : $shopeeConf['partner_id'];

$api = new ShopeeAPI([
    'partner_id'   => $partnerId,
    'partner_key'  => $shopeeConf['partner_key'],
    'shop_id'      => $shopId,
    'access_token' => '',
    'env'          => $shopeeConf['env'],
    'base_url'     => $shopeeConf['base_url'],
]);

$res = $api->getAccessToken($code, $shopId);

if (empty($res['access_token'])) {
    $errMsg = $res['message'] ?? $res['error'] ?? json_encode($res);
    $err    = urlencode('Lỗi lấy Access Token: ' . $errMsg);
    header("Location: /TechFixPHP/pages/admin/shopee_sync.php?tab=settings&error=$err");
    exit;
}

$expireIn     = (int)($res['expire_in']  ?? 14400);
$expires      = date('Y-m-d H:i:s', time() + $expireIn);
$accessToken  = $res['access_token'];
$refreshToken = $res['refresh_token'] ?? '';

$stmt = $conn->prepare("
    UPDATE shopee_settings
       SET shop_id       = ?,
           access_token  = ?,
           refresh_token = ?,
           token_expires = ?
     WHERE id = 1
");
$stmt->bind_param('isss', $shopId, $accessToken, $refreshToken, $expires);
$stmt->execute();

header("Location: /TechFixPHP/pages/admin/shopee_sync.php?tab=settings&saved=1");
exit;
