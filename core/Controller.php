<?php

/**
 * Controller.php — Base controller
 */
abstract class Controller
{
    /**
     * Render một view
     * @param string $view   Đường dẫn tương đối từ app/Views/, VD: 'customer/booking'
     * @param array  $data   Dữ liệu truyền vào view (extract thành biến)
     * @param string $layout Layout wrapper, mặc định 'layouts/main'
     *                       Truyền '' để render view thuần không có layout
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        // Trích xuất data thành biến cục bộ
        extract($data, EXTR_SKIP);

        $viewFile = ROOT . "/app/Views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View không tìm thấy: $viewFile");
        }

        if ($layout === '') {
            // Render thuần, không layout
            require $viewFile;
            return;
        }

        $layoutFile = ROOT . "/app/Views/{$layout}.php";
        if (!file_exists($layoutFile)) {
            // Nếu không có layout, render trực tiếp
            require $viewFile;
            return;
        }

        // Buffer nội dung view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render layout với $content
        require $layoutFile;
    }

    /**
     * Trả về JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Kiểm tra đăng nhập theo role
     */
    protected function requireAuth(string $role = ''): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/TechFixPHP/public/index.php?route=auth/login');
        }
        if ($role && ($_SESSION['role'] ?? '') !== $role) {
            $this->redirect('/TechFixPHP/public/index.php?route=auth/login');
        }
    }

    /**
     * Lấy input từ POST (sanitize cơ bản)
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }

    /**
     * Lấy input từ GET
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }

    /**
     * Flash message (lưu vào session)
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Lấy và xóa flash message
     */
    public static function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
