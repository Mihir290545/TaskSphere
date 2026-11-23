<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Tasks Management</h1>
        <p class="page-subtitle">Track, filter, and collaborate on project tasks.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/tasks/kanban" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h4v16H4zM10 4h4v10h-4zM16 4h4v12h-4z"/></svg>
            Kanban Board
        </a>
        <?php if ($user['role'] !== 'team_member'): ?>
            <a href="/tasks/create" class="btn btn-primary">+ Create Task</a>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1.25rem;">
        <form action="/tasks" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 120px; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Search Title</label>
                <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?= Security::escape($filters['search'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="to_do" <?= ($filters['status'] ?? '') === 'to_do' ? 'selected' : '' ?>>To Do</option>
                    <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="review" <?= ($filters['status'] ?? '') === 'review' ? 'selected' : '' ?>>Review</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="blocked" <?= ($filters['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Project</label>
                <select name="project_id" class="form-select">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($filters['project_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= Security::escape($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-size: 0.75rem;">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest</option>
                    <option value="due_date" <?= ($filters['sort'] ?? '') === 'due_date' ? 'selected' : '' ?>>Due Date</option>
                    <option value="priority" <?= ($filters['sort'] ?? '') === 'priority' ? 'selected' : '' ?>>Priority</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Task Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Task Title</th>
                        <th>Project</th>
                        <th>Team</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Due Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                No tasks matching criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--text-light);">#<?= $task['id'] ?></td>
                                <td>
                                    <a href="/tasks/view?id=<?= $task['id'] ?>" style="font-weight: 600; color: var(--text-main);">
                                        <?= Security::escape($task['title']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--text-muted);"><?= Security::escape($task['project_name']) ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem;"><?= Security::escape($task['team_name']) ?></span>
                                </td>
                                <td><span class="badge badge-<?= $task['priority'] ?>"><?= Security::escape($task['priority']) ?></span></td>
                                <td><span class="badge badge-<?= $task['status'] ?>"><?= Security::escape(str_replace('_', ' ', $task['status'])) ?></span></td>
                                <td>
                                    <span style="font-size: 0.85rem; font-weight: 500;"><?= Security::escape($task['assigned_name'] ?? 'Unassigned') ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $isOverdue = (!empty($task['due_date']) && strtotime($task['due_date']) < time() && $task['status'] !== 'completed');
                                    ?>
                                    <span style="<?= $isOverdue ? 'color: var(--danger); font-weight: 700;' : '' ?>">
                                        <?= $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '—' ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/tasks/view?id=<?= $task['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                    <a href="/tasks/edit?id=<?= $task['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
