<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            $db = $config['db'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $db['username'], $db['password'], $options);
            } catch (PDOException $e) {
                if ($config['debug']) {
                    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
                } else {
                    error_log("Database Error: " . $e->getMessage());
                    die("A database error occurred. Please contact the administrator.");
                }
            }
        }
        return self::$instance;
    }
}
