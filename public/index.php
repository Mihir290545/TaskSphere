<?php
// TaskFlow Public Entrypoint

// Load application configuration
$config = require dirname(__DIR__) . '/config/app.php';

if ($config['debug'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Session;
use App\Core\Router;
use App\Core\Request;

Session::start();

// Load routes
require dirname(__DIR__) . '/routes/web.php';

// Dispatch request
Router::dispatch(Request::method(), Request::uri());
