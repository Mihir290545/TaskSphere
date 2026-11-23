<?php
namespace App\Core;

use App\Helpers\Security;

class Controller {
    protected function render(string $view, array $data = [], string $layout = 'layouts/app'): void {
        View::render($view, $data, $layout);
    }

    protected function json(array $data, int $statusCode = 200): void {
        Response::json($data, $statusCode);
    }

    protected function redirect(string $url): void {
        Response::redirect($url);
    }

    protected function validateCsrf(): void {
        $token = Request::post('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Security::verifyCsrf($token)) {
            if (Request::isAjax()) {
                $this->json(['error' => 'Invalid or expired CSRF token.'], 403);
            }
            Session::flash('error', 'Security token mismatch. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function user(): ?array {
        return Session::get('user');
    }
}
