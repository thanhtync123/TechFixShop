<?php
/**
 * Shopee Open API v2 — Cấu hình
 *
 * Lấy thông tin tại: https://open.shopee.com/
 * Vào: My Apps → chọn app → xem Partner ID, Partner Key
 * Shop ID lấy từ Shopee Seller Center → Cài đặt tài khoản
 */

return [
    // ── Thông tin App ────────────────────────────────────────────────
    'partner_id'  => (int)(getenv('SHOPEE_PARTNER_ID')  ?: 0),   // ← điền Partner ID
    'partner_key' => getenv('SHOPEE_PARTNER_KEY')       ?: '',    // ← điền Partner Key
    'shop_id'     => (int)(getenv('SHOPEE_SHOP_ID')     ?: 0),    // ← điền Shop ID
    'access_token'=> getenv('SHOPEE_ACCESS_TOKEN')      ?: '',    // ← Access Token sau khi auth

    // ── Môi trường ───────────────────────────────────────────────────
    // 'sandbox'  → https://partner.test-stable.shopeemobile.com
    // 'production' → https://partner.shopeemobile.com
    'env'         => 'sandbox',

    // ── URL base ─────────────────────────────────────────────────────
    'base_url' => [
        'sandbox'    => 'https://partner.test-stable.shopeemobile.com',
        'production' => 'https://partner.shopeemobile.com',
    ],

    // ── Ánh xạ danh mục web → Shopee Category ID ────────────────────
    // Tìm category_id tại: GET /api/v2/product/get_category
    'category_map' => [
        'Điện – Điện tử'           => 100644,  // Electronics Services
        'Nước – Môi trường'         => 100645,
        'Thiết bị gia dụng'         => 100646,
        'CNTT – Viễn thông'         => 100647,
        'An toàn – Kiểm định'       => 100648,
        'Bảo trì – Quản lý thiết bị'=> 100649,
        'Dịch vụ đặc biệt'          => 100650,
        'Khác'                      => 100651,
    ],

    // ── Cài đặt mặc định cho listing ────────────────────────────────
    'default_logistics'     => [],   // để trống = Shopee tự chọn
    'condition'             => 'NEW',
    'status'                => 'UNLIST', // UNLIST trước, sau khi review mới NORMAL
];
