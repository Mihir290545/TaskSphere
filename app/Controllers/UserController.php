<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Models\ActivityLog;

class UserController extends Controller {
    public function index(): void {
        $search = Request::get('search', '');
        $role = Request::get('role', '');
        $status = Request::get('status', '');

        $users = User::getAll($search, $role, $status);

        $this->render('users/index', [
            'title' => 'User Management - TaskFlow',
            'users' => $users,
            'search' => $search,
            'roleFilter' => $role,
            'statusFilter' => $status,
            'user' => $this->user()
        ]);
    }

    public function create(): void {
        $this->render('users/create', [
            'title' => 'Create New User - TaskFlow',
            'user' => $this->user()
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        $name = trim((string)Request::post('name', ''));
        $email = strtolower(trim((string)Request::post('email', '')));
        $password = (string)Request::post('password', '');
        $role = Request::post('role', 'team_member');
        $status = Request::post('status', 'active');

        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Name, email, and password are required.');
            $this->redirect('/users/create');
        }

        if (User::findByEmail($email)) {
            Session::flash('error', 'A user with that email already exists.');
            $this->redirect('/users/create');
        }

        $newId = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'status' => $status
        ]);

        $admin = $this->user();
        ActivityLog::log($admin['id'], 'create', 'user', $newId, "Admin created user \"{$name}\" with role {$role}");

        Session::flash('success', 'User created successfully.');
        $this->redirect('/users');
    }

    public function updateStatus(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $status = Request::post('status', 'active');
        $admin = $this->user();

        if ($id === (int)$admin['id']) {
            Session::flash('error', 'You cannot change your own status.');
            $this->redirect('/users');
        }

        User::updateStatus($id, $status);
        ActivityLog::log($admin['id'], 'update_status', 'user', $id, "Admin updated user #{$id} status to {$status}");

        Session::flash('success', 'User status updated.');
        $this->redirect('/users');
    }

    public function updateRole(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $role = Request::post('role', 'team_member');
        $admin = $this->user();

        if ($id === (int)$admin['id']) {
            Session::flash('error', 'You cannot change your own role.');
            $this->redirect('/users');
        }

        User::updateRole($id, $role);
        ActivityLog::log($admin['id'], 'update_role', 'user', $id, "Admin updated user #{$id} role to {$role}");

        Session::flash('success', 'User role updated.');
        $this->redirect('/users');
    }
}
