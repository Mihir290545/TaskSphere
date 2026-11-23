<?php
namespace App\Core;

class Router {
    private static array $routes = [];

    public static function get(string $path, array $handler, array $middleware = []): void {
        self::add('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, array $handler, array $middleware = []): void {
        self::add('POST', $path, $handler, $middleware);
    }

    private static function add(string $method, string $path, array $handler, array $middleware): void {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $handler[0],
            'action' => $handler[1],
            'middleware' => $middleware,
        ];
    }

    public static function dispatch(string $method, string $uri): void {
        // Strip base folder if running under subfolder e.g. /task-team-management-system/public
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . ltrim($uri, '/');
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                // Run middleware
                foreach ($route['middleware'] as $mwClass) {
                    $mw = new $mwClass();
                    $mw->handle();
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $controllerClass = $route['controller'];
                $action = $route['action'];

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    die("Controller [{$controllerClass}] not found.");
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    die("Action [{$action}] not found on [{$controllerClass}].");
                }

                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        // 404 handler
        http_response_code(404);
        View::render('errors/404', ['title' => 'Page Not Found'], 'layouts/auth');
    }
}
