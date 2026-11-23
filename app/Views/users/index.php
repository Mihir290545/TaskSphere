<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">Configure organization members, assign roles, and manage access statuses.</p>
    </div>
    <a href="/users/create" class="btn btn-primary">+ Create User</a>
</div>

<!-- Filters Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1.25rem;">
        <form action="/users" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr 100px; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Search Name or Email</label>
                <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?= Security::escape($search) ?>">
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="team_leader" <?= $roleFilter === 'team_leader' ? 'selected' : '' ?>>Team Leader</option>
                    <option value="team_member" <?= $roleFilter === 'team_member' ? 'selected' : '' ?>>Team Member</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Teams</th>
                        <th>Assigned Tasks</th>
                        <th>Joined</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar-sm" style="width: 34px; height: 34px;">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;"><?= Security::escape($u['name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= Security::escape($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form action="/users/update-role" method="POST" style="margin: 0; display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <select name="role" class="form-select" onchange="this.form.submit()" style="font-size: 0.8rem; padding: 0.35rem 0.6rem; width: auto;" <?= $u['id'] == $user['id'] ? 'disabled' : '' ?>>
                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="team_leader" <?= $u['role'] === 'team_leader' ? 'selected' : '' ?>>Team Leader</option>
                                            <option value="team_member" <?= $u['role'] === 'team_member' ? 'selected' : '' ?>>Team Member</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="/users/update-status" method="POST" style="margin: 0; display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>" style="cursor: pointer; border: none;" <?= $u['id'] == $user['id'] ? 'disabled' : '' ?>>
                                            <?= Security::escape($u['status']) ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?= Security::escape($u['team_names'] ?? 'None') ?></td>
                                <td style="font-weight: 600;"><?= $u['task_count'] ?></td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td style="text-align: right;">
                                    <?php if ($u['id'] == $user['id']): ?>
                                        <span style="font-size: 0.8rem; color: var(--text-light);">(Current user)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
