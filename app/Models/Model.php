<?php
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class Model {
    protected static ?PDO $db = null;

    public static function getDb(): PDO {
        if (self::$db === null) {
            self::$db = Database::connect();
        }
        return self::$db;
    }
}
