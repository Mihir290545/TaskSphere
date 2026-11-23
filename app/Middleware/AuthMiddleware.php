<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class AuthMiddleware {
    public function handle(): void {
        Session::start();
        if (!Session::has('user')) {
            Session::flash('error', 'Please log in to continue.');
            Response::redirect('/login');
        }
    }
}
