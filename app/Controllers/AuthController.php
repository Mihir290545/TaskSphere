<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller {
    public function showLogin(): void {
        $this->render('auth/login', [
            'title' => 'Sign In - TaskFlow'
        ], 'layouts/auth');
    }

    public function login(): void {
        $this->validateCsrf();

        $email = strtolower(trim((string)Request::post('email', '')));
        $password = (string)Request::post('password', '');
        $ip = Request::ip();

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please provide both email and password.');
            $this->redirect('/login');
        }

        // Brute-force rate limiting check
        if (User::isRateLimited($email, $ip)) {
            Session::flash('error', 'Too many failed login attempts. Please wait 15 minutes before trying again.');
            $this->redirect('/login');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            User::recordLoginAttempt($email, $ip);
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            Session::flash('error', 'Your account has been deactivated. Please contact an administrator.');
            $this->redirect('/login');
        }

        // Success - clear attempts and regenerate session
        User::clearLoginAttempts($email, $ip);
        Session::regenerate();

        // Safe user session without password hash
        unset($user['password']);
        Session::set('user', $user);

        ActivityLog::log($user['id'], 'login', 'user', $user['id'], "User {$user['name']} logged in.");
        Session::flash('success', "Welcome back, {$user['name']}!");
        $this->redirect('/dashboard');
    }

    public function showRegister(): void {
        $this->render('auth/register', [
            'title' => 'Create Account - TaskFlow'
        ], 'layouts/auth');
    }

    public function register(): void {
        $this->validateCsrf();

        $name = trim((string)Request::post('name', ''));
        $email = strtolower(trim((string)Request::post('email', '')));
        $password = (string)Request::post('password', '');
        $confirmPassword = (string)Request::post('password_confirmation', '');

        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'All fields are required.');
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('/register');
        }

        if (strlen($password) < 8) {
            Session::flash('error', 'Password must be at least 8 characters long.');
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('/register');
        }

        if (User::findByEmail($email)) {
            Session::flash('error', 'An account with that email already exists.');
            $this->redirect('/register');
        }

        // First user defaults to admin if system is empty, otherwise team_member
        $userCount = (int)User::getDb()->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $role = ($userCount === 0) ? 'admin' : 'team_member';

        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'status' => 'active'
        ]);

        ActivityLog::log($userId, 'register', 'user', $userId, "New user {$name} registered as {$role}.");

        // Log the newly registered user in automatically
        Session::regenerate();
        $newUser = User::findById($userId);
        unset($newUser['password']);
        Session::set('user', $newUser);

        Session::flash('success', 'Account created successfully! Welcome to TaskFlow.');
        $this->redirect('/dashboard');
    }

    public function logout(): void {
        $user = Session::get('user');
        if ($user) {
            ActivityLog::log($user['id'], 'logout', 'user', $user['id'], "User {$user['name']} logged out.");
        }
        Session::destroy();
        Session::start();
        Session::flash('success', 'You have been safely logged out.');
        $this->redirect('/login');
    }
}
