<?php
namespace App\Core;

class View {
    public static function render(string $view, array $data = [], string $layout = 'layouts/app'): void {
        extract($data);
        $contentFile = dirname(__DIR__) . "/Views/{$view}.php";

        if (!file_exists($contentFile)) {
            http_response_code(500);
            die("View [{$view}] not found.");
        }

        // Buffer view content
        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = dirname(__DIR__) . "/Views/{$layout}.php";
            if (file_exists($layoutFile)) {
                require $layoutFile;
                return;
            }
        }

        echo $content;
    }
}
