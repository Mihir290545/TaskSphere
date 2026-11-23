<?php
namespace App\Middleware;

use App\Core\Session;
use App\Core\Response;

class LeaderOrAdminMiddleware {
    public function handle(): void {
        Session::start();
        $user = Session::get('user');
        if (!$user || !in_array($user['role'], ['admin', 'team_leader'])) {
            Session::flash('error', 'Unauthorized access. Team leader or admin rights required.');
            Response::redirect('/dashboard');
        }
    }
}
