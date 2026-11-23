<?php
use App\Helpers\Security;
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::escape($title ?? 'TaskFlow - Login') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary), #818cf8); border-radius: var(--radius-md); color: white; font-weight: 800; font-size: 1.35rem; margin-bottom: 0.75rem;">
                T
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">TaskFlow</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">Team Collaboration & Task Management</p>
        </div>

        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success">
                <span><?= Security::escape(Session::flash('success')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-error">
                <span><?= Security::escape(Session::flash('error')) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</div>
</body>
</html>
