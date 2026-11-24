<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<form action="/login" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group">
        <label class="form-label" for="email">Work Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="name@company.com" required autofocus>
    </div>

    <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
        </div>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>

    <div style="margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
    </div>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-muted);">
        Don't have an account? <a href="/register" style="color: var(--primary); font-weight: 600;">Create one</a>
    </div>
</form>
