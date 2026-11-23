<?php
namespace App\Core;

use App\Helpers\Security;

class Request {
    public static function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function uri(): string {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        // Normalize trailing slashes unless root
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }
        return $uri;
    }

    public static function get(string $key, $default = null) {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }

    public static function post(string $key, $default = null) {
        return isset($_POST[$key]) ? (is_array($_POST[$key]) ? $_POST[$key] : trim((string)$_POST[$key])) : $default;
    }

    public static function all(): array {
        $method = self::method();
        if ($method === 'GET') {
            return Security::cleanInput($_GET);
        }
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?? [];
            return Security::cleanInput($data);
        }

        return Security::cleanInput($_POST);
    }

    public static function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }

    public static function ip(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
