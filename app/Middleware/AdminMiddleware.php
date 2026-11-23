<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class AdminMiddleware {
    public function handle(): void {
        Session::start();
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            Session::flash('error', 'Unauthorized access. Administrator privileges required.');
            Response::redirect('/dashboard');
        }
    }
}
