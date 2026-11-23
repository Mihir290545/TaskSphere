<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
$canManageMembers = ($user['role'] === 'admin' || ($user['role'] === 'team_leader' && $team['leader_id'] == $user['id']));
?>
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="/teams" style="font-size: 0.85rem; color: var(--primary);">← Back to Teams</a>
        </div>
        <h1 class="page-title"><?= Security::escape($team['name']) ?></h1>
        <p class="page-subtitle">Team Leader: <b><?= Security::escape($team['leader_name'] ?? 'Unassigned') ?></b></p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if ($canManageMembers): ?>
            <a href="/teams/edit?id=<?= $team['id'] ?>" class="btn btn-secondary">Edit Team</a>
        <?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?>
            <form action="/teams/delete" method="POST" onsubmit="return confirm('Delete this team?');" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $team['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <!-- Team Description -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">About Team</h2>
            </div>
            <div class="card-body">
                <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-main);">
                    <?= Security::escape($team['description'] ?: 'No team description.') ?>
                </p>
            </div>
        </div>

        <!-- Team Tasks -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Team Tasks (<?= count($tasks) ?>)</h2>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Assignee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasks)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No tasks assigned to this team.</td></tr>
                            <?php else: ?>
                                <?php foreach ($tasks as $t): ?>
                                    <tr>
                                        <td>
                                            <a href="/tasks/view?id=<?= $t['id'] ?>" style="font-weight: 600; color: var(--text-main);">
                                                <?= Security::escape($t['title']) ?>
                                            </a>
                                        </td>
                                        <td><span class="badge badge-<?= $t['status'] ?>"><?= Security::escape(str_replace('_', ' ', $t['status'])) ?></span></td>
                                        <td><span class="badge badge-<?= $t['priority'] ?>"><?= Security::escape($t['priority']) ?></span></td>
                                        <td><?= Security::escape($t['assigned_name'] ?? 'Unassigned') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Panel -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Team Roster (<?= count($members) ?>)</h2>
            </div>
            <div class="card-body">
                <?php if ($canManageMembers): ?>
                    <!-- Add Member Form -->
                    <form action="/teams/members/add" method="POST" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem;">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                        <select name="user_id" class="form-select" required style="font-size: 0.85rem;">
                            <option value="">Select User to Add</option>
                            <?php foreach ($allUsers as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= Security::escape($u['name']) ?> (<?= $u['role'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">+ Add</button>
                    </form>
                <?php endif; ?>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php if (empty($members)): ?>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">No members currently in team.</div>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-light); padding-bottom: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem;"><?= Security::escape($m['name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= Security::escape($m['email']) ?></div>
                                    </div>
                                </div>
                                <?php if ($canManageMembers && $m['id'] != $team['leader_id']): ?>
                                    <form action="/teams/members/remove" method="POST" onsubmit="return confirm('Remove user from team?');" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $m['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--danger); font-size: 0.75rem; cursor: pointer;">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
