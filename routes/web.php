<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TaskController;
use App\Controllers\ProjectController;
use App\Controllers\TeamController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\NotificationController;
use App\Controllers\ActivityController;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\LeaderOrAdminMiddleware;

// Guest Routes
Router::get('/', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
Router::get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
Router::post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
Router::get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
Router::post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);

// Authenticated Routes
Router::post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
Router::get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// Tasks
Router::get('/tasks', [TaskController::class, 'index'], [AuthMiddleware::class]);
Router::get('/tasks/kanban', [TaskController::class, 'kanban'], [AuthMiddleware::class]);
Router::get('/tasks/view', [TaskController::class, 'view'], [AuthMiddleware::class]);
Router::get('/tasks/create', [TaskController::class, 'create'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/tasks/store', [TaskController::class, 'store'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::get('/tasks/edit', [TaskController::class, 'edit'], [AuthMiddleware::class]);
Router::post('/tasks/update', [TaskController::class, 'update'], [AuthMiddleware::class]);
Router::post('/tasks/update-status-ajax', [TaskController::class, 'updateStatusAjax'], [AuthMiddleware::class]);
Router::post('/tasks/delete', [TaskController::class, 'delete'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/tasks/comment', [TaskController::class, 'addComment'], [AuthMiddleware::class]);
Router::post('/tasks/comment/delete', [TaskController::class, 'deleteComment'], [AuthMiddleware::class]);

// Projects
Router::get('/projects', [ProjectController::class, 'index'], [AuthMiddleware::class]);
Router::get('/projects/view', [ProjectController::class, 'view'], [AuthMiddleware::class]);
Router::get('/projects/create', [ProjectController::class, 'create'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/projects/store', [ProjectController::class, 'store'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::get('/projects/edit', [ProjectController::class, 'edit'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/projects/update', [ProjectController::class, 'update'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/projects/delete', [ProjectController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class]);

// Teams
Router::get('/teams', [TeamController::class, 'index'], [AuthMiddleware::class]);
Router::get('/teams/view', [TeamController::class, 'view'], [AuthMiddleware::class]);
Router::get('/teams/create', [TeamController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/teams/store', [TeamController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/teams/edit', [TeamController::class, 'edit'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/teams/update', [TeamController::class, 'update'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/teams/delete', [TeamController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/teams/members/add', [TeamController::class, 'addMember'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);
Router::post('/teams/members/remove', [TeamController::class, 'removeMember'], [AuthMiddleware::class, LeaderOrAdminMiddleware::class]);

// Users (Admin Only)
Router::get('/users', [UserController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/users/create', [UserController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/users/store', [UserController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/users/update-status', [UserController::class, 'updateStatus'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/users/update-role', [UserController::class, 'updateRole'], [AuthMiddleware::class, AdminMiddleware::class]);

// Profile
Router::get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class]);
Router::post('/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class]);
Router::post('/profile/password', [ProfileController::class, 'changePassword'], [AuthMiddleware::class]);

// Notifications
Router::get('/notifications', [NotificationController::class, 'index'], [AuthMiddleware::class]);
Router::post('/notifications/read', [NotificationController::class, 'markAsRead'], [AuthMiddleware::class]);
Router::post('/notifications/read-all', [NotificationController::class, 'markAllRead'], [AuthMiddleware::class]);

// Activity Logs
Router::get('/activity', [ActivityController::class, 'index'], [AuthMiddleware::class]);
