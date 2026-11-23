<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
$isMember = ($user['role'] === 'team_member');
?>
<div class="page-header">
    <div>
        <a href="/tasks/view?id=<?= $task['id'] ?>" style="font-size: 0.85rem; color: var(--primary);">← Back to Task</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Edit Task #<?= $task['id'] ?></h1>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="/tasks/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="id" value="<?= $task['id'] ?>">

            <div class="form-group">
                <label class="form-label" for="title">Task Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= Security::escape($task['title']) ?>" <?= $isMember ? 'readonly' : 'required' ?>>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= Security::escape($task['description']) ?></textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="team_id">Team *</label>
                    <select id="team_id" name="team_id" class="form-select" <?= $isMember ? 'disabled' : 'required' ?>>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $task['team_id'] == $t['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="project_id">Project *</label>
                    <select id="project_id" name="project_id" class="form-select" <?= $isMember ? 'disabled' : 'required' ?>>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $task['project_id'] == $p['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="assigned_to">Assignee</label>
                    <select id="assigned_to" name="assigned_to" class="form-select" <?= $isMember ? 'disabled' : '' ?>>
                        <option value="">Unassigned</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= $task['assigned_to'] == $m['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($m['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-select" <?= $isMember ? 'disabled' : '' ?>>
                        <option value="low" <?= $task['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $task['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $task['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= $task['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="to_do" <?= $task['status'] === 'to_do' ? 'selected' : '' ?>>To Do</option>
                        <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="review" <?= $task['status'] === 'review' ? 'selected' : '' ?>>Review</option>
                        <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="blocked" <?= $task['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" class="form-control" value="<?= $task['due_date'] ?>" <?= $isMember ? 'readonly' : '' ?>>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/tasks/view?id=<?= $task['id'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
