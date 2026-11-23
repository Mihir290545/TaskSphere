<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="/projects" style="font-size: 0.85rem; color: var(--primary);">← Back to Projects</a>
            <span class="badge badge-<?= $project['status'] ?>"><?= Security::escape($project['status']) ?></span>
        </div>
        <h1 class="page-title"><?= Security::escape($project['name']) ?></h1>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if ($user['role'] !== 'team_member'): ?>
            <a href="/projects/edit?id=<?= $project['id'] ?>" class="btn btn-secondary">Edit Project</a>
            <a href="/tasks/create" class="btn btn-primary">+ Add Task</a>
        <?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?>
            <form action="/projects/delete" method="POST" onsubmit="return confirm('Delete this project and all its tasks?');" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin-bottom: 1.25rem;">
            <?= Security::escape($project['description'] ?: 'No project description provided.') ?>
        </p>

        <div style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.875rem; border-top: 1px solid var(--border-light); padding-top: 1rem;">
            <div>
                <span style="color: var(--text-muted);">Assigned Team:</span>
                <strong><?= Security::escape($project['team_name']) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Project Manager:</span>
                <strong><?= Security::escape($project['manager_name'] ?? 'None') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Start Date:</span>
                <strong><?= $project['start_date'] ? date('M d, Y', strtotime($project['start_date'])) : '—' ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Deadline:</span>
                <strong><?= $project['deadline'] ? date('M d, Y', strtotime($project['deadline'])) : 'Open' ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Completion:</span>
                <strong><?= $project['progress'] ?>% (<?= $project['completed_tasks'] ?> / <?= $project['total_tasks'] ?> tasks)</strong>
            </div>
        </div>
    </div>
</div>

<!-- Project Tasks List -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Project Tasks (<?= count($tasks) ?>)</h2>
        <?php if ($user['role'] !== 'team_member'): ?>
            <a href="/tasks/create" class="btn btn-secondary btn-sm">+ New Task</a>
        <?php endif; ?>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Due Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No tasks added to this project yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $t): ?>
                            <tr>
                                <td>
                                    <a href="/tasks/view?id=<?= $t['id'] ?>" style="font-weight: 600; color: var(--text-main);">
                                        <?= Security::escape($t['title']) ?>
                                    </a>
                                </td>
                                <td><span class="badge badge-<?= $t['priority'] ?>"><?= Security::escape($t['priority']) ?></span></td>
                                <td><span class="badge badge-<?= $t['status'] ?>"><?= Security::escape(str_replace('_', ' ', $t['status'])) ?></span></td>
                                <td><?= Security::escape($t['assigned_name'] ?? 'Unassigned') ?></td>
                                <td><?= $t['due_date'] ? date('M d, Y', strtotime($t['due_date'])) : '—' ?></td>
                                <td style="text-align: right;">
                                    <a href="/tasks/view?id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
