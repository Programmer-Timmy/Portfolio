<?php
class Router {
    private static array $routes = [];
    private static array $routePriorities = [];
    private static array $routeSEO = [];
    private static ?string $currentRoute = null;

    public static function get(string $pattern, callable $callback, float $priority = 0.80, array $seo = [], bool $cleanPage = false) {
        self::$routes['GET'][] = ['pattern' => $pattern, 'callback' => $callback, 'cleanPage' => $cleanPage];
        self::$routePriorities[$pattern] = $priority;
        self::$routeSEO[$pattern] = array_merge([
            'title' => '',
            'description' => '',
            'keywords' => '',
            'og_image' => '',
            'robots' => 'index, follow'
        ], $seo);


    }

    public static function post(string $pattern, callable $callback, float $priority = 0.80, array $seo = []) {
        self::$routes['POST'][] = ['pattern' => $pattern, 'callback' => $callback];
        if (!isset(self::$routePriorities[$pattern])) {
            self::$routePriorities[$pattern] = $priority;
        }
        if (!isset(self::$routeSEO[$pattern])) {
            self::$routeSEO[$pattern] = array_merge([
                'title' => '',
                'description' => '',
                'keywords' => '',
                'og_image' => '',
                'robots' => 'index, follow'
            ], $seo);
        }
    }

    public static function dispatch(): bool {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if ($uri === '') {
            $uri = 'home';
        }

        foreach (self::$routes[$method] ?? [] as $route) {
            $pattern = self::createPattern($route['pattern']);
            if (preg_match($pattern, $uri, $matches)) {
                self::$currentRoute = $route['pattern'];
                array_shift($matches);
                if (!$route['cleanPage']) {
                    self::includeHeader();
                    self::includeNavbar();
                }
                call_user_func_array($route['callback'], $matches);
                if (!$route['cleanPage']) {
                    self::includeFooter();
                    self::handlePopup();
                }
                return true;
            }
        }

        // No route matched - show 404
        self::$currentRoute = '404';
        self::includeHeader();
        self::includeNavbar();
        self::handle404('/' . $uri);
        self::includeFooter();
        self::handlePopup();
        return false;
    }

    public static function isRoute(string $uri, bool $ignoreMethod = false): bool {
        if ($ignoreMethod) {
            foreach (self::$routes as $methodRoutes) {
                foreach ($methodRoutes as $route) {
                    $pattern = self::createPattern($route['pattern']);
                    if (preg_match($pattern, $uri)) {
                        return true;
                    }
                }
            }
            return false;
        }

        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes[$method] ?? [] as $route) {
            $pattern = self::createPattern($route['pattern']);
            if (preg_match($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    public static function getRoutes(): array {
        return self::$routes;
    }

    public static function getAllRoutePatterns(): array {
        $patterns = [];
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $route) {
                if (!in_array($route['pattern'], $patterns)) {
                    $patterns[] = $route['pattern'];
                }
            }
        }
        return $patterns;
    }

    public static function getRoutePriority(string $pattern): float {
        return self::$routePriorities[$pattern] ?? 0.80;
    }

    public static function getCurrentSEO(): array {
        global $site;
        if (self::$currentRoute === null) {
            return [
                'title' => $site['siteName'],
                'description' => '',
                'keywords' => '',
                'og_image' => '',
                'robots' => 'index, follow'
            ];
        }

        return self::$routeSEO[self::$currentRoute] ?? [
            'title' => $site['siteName'],
            'description' => '',
            'keywords' => '',
            'og_image' => '',
            'robots' => 'index, follow'
        ];
    }

    public static function getPageTitle(): string {
        global $site;
        $seo = self::getCurrentSEO();
        return $seo['title'] ?: $site['siteName'];
    }

    public static function getPageDescription(): string {
        $seo = self::getCurrentSEO();
        return $seo['description'];
    }

    public static function getPageKeywords(): string {
        $seo = self::getCurrentSEO();
        return $seo['keywords'];
    }

    public static function getOGImage(): string {
        $seo = self::getCurrentSEO();
        return $seo['og_image'];
    }

    public static function getRobots(): string {
        $seo = self::getCurrentSEO();
        return $seo['robots'];
    }

    private static function createPattern(string $pattern): string {
        return "#^" . preg_replace('#\{[a-zA-Z_]+\}#', '([a-zA-Z0-9_-]+)', $pattern) . "$#";
    }

    private static function handle404(string $requestedPage): void {
        http_response_code(404);
        include __DIR__ . '/../views/pages/404.php';
    }

    private static function includeFooter(): void {
        include __DIR__ . '/../views/templates/footer.php';
    }

    private static function includeNavbar(): void {
        include __DIR__ . '/../views/templates/navbar.php';
    }

    private static function includeHeader(): void {
        include __DIR__ . '/../views/templates/header.php';
    }

    private static function includeMaintenance(): void {
        include __DIR__ . '/../views/pages/maintenance.php';
    }

    private static function handlePopup(): void {
        global $site;

        if (!empty($site['showPopup']) && !isset($_SESSION['popupShown'])) {
            include __DIR__ . '/../views/Popups/popup.php';
            $_SESSION['popupShown'] = true;
        }
    }

}
