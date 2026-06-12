<!-- ========== FOOTER ========== -->
<footer class="tf-footer">
    <div class="tf-footer-container">

        <div class="tf-footer-brand">
            <div class="tf-logo" style="margin-bottom:10px;">
                <div class="tf-logo-mark"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                <span>TECHFIX</span>
            </div>
            <p>Sửa chữa tận tâm – Nâng tầm cuộc sống</p>
        </div>

        <div class="tf-footer-links">
            <h4>Liên kết nhanh</h4>
            <ul>
                <li><a href="/TechFixPHP/index.php">Trang chủ</a></li>
                <li><a href="/TechFixPHP/Customer/Service.php">Dịch vụ</a></li>
                <li><a href="/TechFixPHP/Customer/book.php">Đặt lịch</a></li>
                <li><a href="/TechFixPHP/Customer/my_booking.php">Lịch của tôi</a></li>
            </ul>
        </div>

        <div class="tf-footer-contact">
            <h4>Liên hệ</h4>
            <p><i class="fa-solid fa-envelope"></i> support@techfix.com</p>
            <p><i class="fa-solid fa-phone"></i> +84 123 456 789</p>
            <p><i class="fa-solid fa-location-dot"></i> P4 Phạm Thái Bường, Vĩnh Long</p>
        </div>

    </div>
    <div class="tf-footer-bottom">
        <p>© <?= date('Y') ?> TECHFIX. All rights reserved.</p>
    </div>
</footer>

<!-- Toast container dùng chung -->
<div id="tf-toast" class="tf-toast" role="alert" aria-live="polite"></div>

<script>
/**
 * showToast(message, type)
 * type: 'success' | 'error' | 'warning' | 'info'
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('tf-toast');
    if (!toast) return;
    const icons = { success: '✓', error: '✗', warning: '⚠', info: 'ℹ' };
    toast.className = 'tf-toast tf-toast-' + type + ' tf-toast-show';
    toast.innerHTML = `<span>${icons[type] || ''}</span> ${message}`;
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('tf-toast-show');
    }, 3500);
}
</script>

</body>
</html>
