<?php
namespace App\Helpers;

use App\Core\Session;

class Security {
    public static function generateCsrf(): string {
        $token = Session::get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }
        return $token;
    }

    public static function verifyCsrf(?string $token): bool {
        $stored = Session::get('csrf_token');
        if (!$stored || !$token) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function escape(?string $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function cleanInput(array $data): array {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::cleanInput($value);
            } else {
                $sanitized[$key] = trim((string)$value);
            }
        }
        return $sanitized;
    }
}
