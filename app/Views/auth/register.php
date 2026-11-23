<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<form action="/register" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Jane Doe" required autofocus>
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Work Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="jane@company.com" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password (Min. 8 characters)</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8">
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••" required minlength="8">
    </div>

    <div style="margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
    </div>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-muted);">
        Already have an account? <a href="/login" style="color: var(--primary); font-weight: 600;">Sign In</a>
    </div>
</form>
