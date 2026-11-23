<?php
// Configuration Loader

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        static $env = null;
        if ($env === null) {
            $envPath = dirname(__DIR__) . '/.env';
            $env = [];
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (str_starts_with(trim($line), '#')) continue;
                    if (strpos($line, '=') !== false) {
                        list($name, $val) = explode('=', $line, 2);
                        $name = trim($name);
                        $val = trim($val, " \t\n\r\0\x0B\"'");
                        $env[$name] = $val;
                    }
                }
            }
        }
        return $env[$key] ?? $default;
    }
}

return [
    'name' => env('APP_NAME', 'TaskFlow'),
    'env' => env('APP_ENV', 'development'),
    'debug' => env('APP_DEBUG', 'true') === 'true',
    'url' => env('APP_URL', 'http://localhost:8000'),
    'session_lifetime' => (int)env('SESSION_LIFETIME', 7200),
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_NAME', 'taskflow_db'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ]
];
