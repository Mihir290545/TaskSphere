<?php
require_once dirname(__DIR__) . '/config/app.php';
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;

$hash = password_hash('Password123!', PASSWORD_BCRYPT);
$db = Database::connect();
$stmt = $db->prepare("UPDATE users SET password = :hash");
$stmt->execute(['hash' => $hash]);

echo "All users updated to password Password123! with hash: " . $hash . "\n";
