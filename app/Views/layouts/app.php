<?php
use App\Helpers\Security;
use App\Core\Session;
use App\Models\Notification;

$currentUser = Session::get('user');
$unreadNotifs = $currentUser ? Notification::getUnreadCount($currentUser['id']) : 0;
$csrfToken = Security::generateCsrf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <title><?= Security::escape($title ?? 'TaskFlow') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="/dashboard" class="brand">
                <div class="brand-icon">T</div>
                <span>TaskFlow</span>
            </a>
        </div>

        <nav class="nav-menu">
            <span class="nav-label">Main Menu</span>
            <a href="/dashboard" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h7v9H3zM14 3h7v4h-7zM14 11h7v10h-7zM3 16h7v5H3z"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="/tasks" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/tasks') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'kanban') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span>Tasks List</span>
            </a>
            <a href="/tasks/kanban" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/tasks/kanban') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h4v16H4zM10 4h4v10h-4zM16 4h4v12h-4z"/></svg>
                <span>Kanban Board</span>
            </a>
            <a href="/projects" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/projects') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span>Projects</span>
            </a>
            <a href="/teams" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/teams') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                <span>Teams</span>
            </a>

            <span class="nav-label">Collaboration</span>
            <a href="/notifications" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/notifications') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                <span>Notifications</span>
            </a>
            <a href="/activity" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/activity') ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Activity Feed</span>
            </a>

            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                <span class="nav-label">Administration</span>
                <a href="/users" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>User Management</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-avatar-sm">
                <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="user-info-sm">
                <div class="user-name-sm"><?= Security::escape($currentUser['name'] ?? '') ?></div>
                <div class="user-role-badge"><?= str_replace('_', ' ', $currentUser['role'] ?? '') ?></div>
            </div>
            <form action="/logout" method="POST" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" title="Logout" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <header class="topbar">
            <div style="display: flex; align-items: center;">
                <button class="mobile-menu-toggle" aria-label="Toggle menu">☰</button>
                <div class="breadcrumbs">
                    <span>TaskFlow</span> / <span><?= Security::escape($title ?? 'Dashboard') ?></span>
                </div>
            </div>

            <div class="topbar-actions">
                <a href="/notifications" class="notif-badge-btn" title="Notifications">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                    <?php if ($unreadNotifs > 0): ?>
                        <span class="notif-count"><?= $unreadNotifs ?></span>
                    <?php endif; ?>
                </a>

                <a href="/profile" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 0.5rem;">
                    <span>Profile</span>
                </a>
            </div>
        </header>

        <main class="content-area">
            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span><?= Security::escape(Session::flash('success')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    <span><?= Security::escape(Session::flash('error')) ?></span>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
