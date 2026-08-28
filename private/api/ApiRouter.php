<?php

/** Minimal method + path router for the `/api` namespace. */
class ApiRouter
{
    /** @var array<int, array{method:string, pattern:string, handler:callable}> */
    private array $routes = [];
    private bool $pathMatchedOtherMethod = false;

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $this->compile($path),
            'handler' => $handler,
        ];
    }

    private function compile(string $path): string
    {
        $path = trim($path, '/');
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
        $path = preg_replace('#^api/?#', '', $uri);

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            if ($route['method'] !== $method) {
                $this->pathMatchedOtherMethod = true;
                continue;
            }

            $params = array_filter(
                $matches,
                static fn ($key) => is_string($key),
                ARRAY_FILTER_USE_KEY,
            );

            ApiResponse::send(call_user_func($route['handler'], $params));
            return;
        }

        if ($this->pathMatchedOtherMethod) {
            throw ApiException::methodNotAllowed("$method is not allowed on /$uri");
        }

        throw ApiException::notFound("No API route matches /$uri");
    }
}
