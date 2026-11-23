<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/users" style="font-size: 0.85rem; color: var(--primary);">← Back to Users</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Create New User</h1>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="/users/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Johnathan Smith" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Work Email *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@company.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Temporary Password * (Min 8 chars)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8">
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="role">User Role *</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="team_member" selected>Team Member</option>
                        <option value="team_leader">Team Leader</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Account Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
