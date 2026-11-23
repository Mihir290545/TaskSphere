<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class GuestMiddleware {
    public function handle(): void {
        Session::start();
        if (Session::has('user')) {
            Response::redirect('/dashboard');
        }
    }
}
