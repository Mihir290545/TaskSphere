<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">User Profile</h1>
        <p class="page-subtitle">Manage your account information and credentials.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Profile Info Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Profile Details</h2>
        </div>
        <div class="card-body">
            <form action="/profile/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= Security::escape($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" class="form-control" value="<?= Security::escape($user['email']) ?>" disabled>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Email address is locked for compliance.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?= strtoupper(str_replace('_', ' ', $user['role'])) ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Joined On</label>
                    <input type="text" class="form-control" value="<?= date('F d, Y', strtotime($user['created_at'])) ?>" disabled>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Security & Password</h2>
        </div>
        <div class="card-body">
            <form action="/profile/password" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password * (Min 8 chars)</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm New Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Update Password</button>
            </form>
        </div>
    </div>
</div>
