<?php

/**
 * Router.php — URL dispatcher đơn giản
 *
 * Cách dùng:
 *   GET  /booking          → BookingController@index
 *   POST /booking/store    → BookingController@store
 *   GET  /admin/dashboard  → AdminController@dashboard
 *
 * URL pattern: /TechFixPHP/public/?route=controller/method[/param]
 * Hoặc dùng .htaccess rewrite để URL đẹp hơn
 */
class Router
{
    private array $routes = [];

    /** Đăng ký route GET */
    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = [$controller, $method];
    }

    /** Đăng ký route POST */
    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = [$controller, $method];
    }

    /** Đăng ký cả GET lẫn POST */
    public function any(string $path, string $controller, string $method): void
    {
        $this->get($path, $controller, $method);
        $this->post($path, $controller, $method);
    }

    /**
     * Dispatch request hiện tại
     */
    public function dispatch(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri        = $this->resolveUri();

        // Thử khớp chính xác trước
        if (isset($this->routes[$httpMethod][$uri])) {
            [$controllerName, $method] = $this->routes[$httpMethod][$uri];
            $this->callAction($controllerName, $method);
            return;
        }

        // Thử khớp có wildcard (/:id)
        foreach ($this->routes[$httpMethod] ?? [] as $pattern => $action) {
            $regex = '#^' . preg_replace('/:([^/]+)/', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                // Truyền các param vào $_GET để controller lấy được
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) $_GET[$k] = $v;
                }
                [$controllerName, $method] = $action;
                $this->callAction($controllerName, $method);
                return;
            }
        }

        // 404
        http_response_code(404);
        $notFound = ROOT . '/app/Views/errors/404.php';
        if (file_exists($notFound)) {
            require $notFound;
        } else {
            echo '<h1>404 – Không tìm thấy trang</h1>';
        }
    }

    /** Resolve URI từ query string hoặc PATH_INFO */
    private function resolveUri(): string
    {
        // Ưu tiên ?route= (dễ dùng với XAMPP không cần rewrite)
        if (!empty($_GET['route'])) {
            return '/' . ltrim($_GET['route'], '/');
        }

        // Nếu có mod_rewrite → dùng PATH_INFO hoặc REQUEST_URI
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Loại bỏ base path /TechFixPHP/public
        $base = '/TechFixPHP/public';
        if (str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        return $uri ?: '/';
    }

    /** Khởi tạo controller và gọi method */
    private function callAction(string $controllerName, string $method): void
    {
        $file = ROOT . "/app/Controllers/{$controllerName}.php";

        if (!file_exists($file)) {
            throw new \RuntimeException("Controller không tìm thấy: $file");
        }

        require_once $file;

        if (!class_exists($controllerName)) {
            throw new \RuntimeException("Class $controllerName không tồn tại");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method $controllerName::$method không tồn tại");
        }

        $controller->$method();
    }
}
