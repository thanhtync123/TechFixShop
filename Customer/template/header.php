<?php
/**
 * Shared header / navbar cho tất cả trang Customer
 * Usage: include __DIR__ . '/template/header.php';
 *
 * Biến tuỳ chọn trước khi include:
 *   $pageTitle  – tiêu đề tab trình duyệt (mặc định "TECHFIX")
 *   $extraCss   – mảng đường dẫn CSS bổ sung
 */

$pageTitle  = $pageTitle  ?? 'TECHFIX – Sửa chữa tận tâm';
$extraCss   = $extraCss   ?? [];

$isLoggedIn  = isset($_SESSION['user']);
$customerName = $_SESSION['user']['name'] ?? '';
$role        = $_SESSION['role'] ?? '';

$currentFile = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS dùng chung cho Customer -->
    <link rel="stylesheet" href="/TechFixPHP/assets/css/customer_shared.css">

    <?php foreach ($extraCss as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="tf-navbar">
    <div class="tf-nav-container">

        <!-- Logo -->
        <a href="/TechFixPHP/index.php" class="tf-logo">
            <div class="tf-logo-mark">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <span>TECHFIX</span>
        </a>

        <!-- Desktop links -->
        <ul class="tf-nav-links" id="tfNavLinks">
            <li><a href="/TechFixPHP/index.php"
                   class="<?= $currentFile === 'index.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a></li>
            <li><a href="/TechFixPHP/Customer/Service.php"
                   class="<?= $currentFile === 'Service.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-list-check"></i> Dịch vụ
            </a></li>
            <li><a href="/TechFixPHP/Customer/book.php"
                   class="<?= $currentFile === 'book.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-plus"></i> Đặt lịch
            </a></li>
            <?php if ($isLoggedIn && $role === 'customer'): ?>
            <li><a href="/TechFixPHP/Customer/my_booking.php"
                   class="<?= $currentFile === 'my_booking.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Lịch của tôi
            </a></li>
            <?php endif; ?>
        </ul>

        <!-- Auth area -->
        <div class="tf-nav-auth">
            <?php if ($isLoggedIn): ?>
                <div class="tf-user-chip">
                    <i class="fa-solid fa-circle-user"></i>
                    <span class="tf-user-name"><?= htmlspecialchars($customerName) ?></span>
                </div>
                <a href="/TechFixPHP/pages/public_page/logout.php?action=logout"
                   class="tf-btn tf-btn-outline tf-btn-sm"
                   title="Đăng xuất">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hide-xs">Đăng xuất</span>
                </a>
            <?php else: ?>
                <a href="/TechFixPHP/pages/public_page/login.php"
                   class="tf-btn tf-btn-outline tf-btn-sm">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </a>
                <a href="/TechFixPHP/pages/public_page/register.php"
                   class="tf-btn tf-btn-primary tf-btn-sm">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký
                </a>
            <?php endif; ?>
        </div>

        <!-- Hamburger -->
        <button class="tf-hamburger" id="tfHamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile menu -->
<div class="tf-mobile-menu" id="tfMobileMenu">
    <a href="/TechFixPHP/index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
    <a href="/TechFixPHP/Customer/Service.php"><i class="fa-solid fa-list-check"></i> Dịch vụ</a>
    <a href="/TechFixPHP/Customer/book.php"><i class="fa-solid fa-calendar-plus"></i> Đặt lịch</a>
    <?php if ($isLoggedIn && $role === 'customer'): ?>
    <a href="/TechFixPHP/Customer/my_booking.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch của tôi</a>
    <?php endif; ?>
    <hr style="border-color:rgba(255,255,255,0.15); margin:8px 0;">
    <?php if ($isLoggedIn): ?>
        <a href="/TechFixPHP/pages/public_page/logout.php?action=logout" style="color:#f87171;">
            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất (<?= htmlspecialchars($customerName) ?>)
        </a>
    <?php else: ?>
        <a href="/TechFixPHP/pages/public_page/login.php"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
        <a href="/TechFixPHP/pages/public_page/register.php"><i class="fa-solid fa-user-plus"></i> Đăng ký</a>
    <?php endif; ?>
</div>

<!-- Spacer đẩy nội dung xuống dưới navbar cố định -->
<div class="tf-nav-spacer"></div>

<script>
(function() {
    const btn  = document.getElementById('tfHamburger');
    const menu = document.getElementById('tfMobileMenu');
    btn.addEventListener('click', function() {
        const open = menu.classList.toggle('open');
        btn.classList.toggle('open', open);
    });
    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('open');
            btn.classList.remove('open');
        }
    });
})();
</script>
