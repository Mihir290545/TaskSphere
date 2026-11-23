<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/tasks" style="font-size: 0.85rem; color: var(--primary);">← Back to Tasks</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Create New Task</h1>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="/tasks/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label" for="title">Task Title *</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Implement OAuth login provider" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Detail specifications, goals and deliverables..."></textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="team_id">Team *</label>
                    <select id="team_id" name="team_id" class="form-select" required>
                        <option value="">Select Team</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= Security::escape($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="project_id">Project *</label>
                    <select id="project_id" name="project_id" class="form-select" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= Security::escape($p['name']) ?> (<?= Security::escape($p['team_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="assigned_to">Assignee</label>
                    <select id="assigned_to" name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= Security::escape($m['name']) ?> (<?= Security::escape($m['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="status">Initial Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="to_do" selected>To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="completed">Completed</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" class="form-control">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Create Task</button>
                <a href="/tasks" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
