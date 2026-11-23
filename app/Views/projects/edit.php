<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/projects/view?id=<?= $project['id'] ?>" style="font-size: 0.85rem; color: var(--primary);">← Back to Project</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Edit Project: <?= Security::escape($project['name']) ?></h1>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="/projects/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <div class="form-group">
                <label class="form-label" for="name">Project Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= Security::escape($project['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= Security::escape($project['description']) ?></textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="team_id">Assigned Team *</label>
                    <select id="team_id" name="team_id" class="form-select" required>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $project['team_id'] == $t['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="manager_id">Project Manager</label>
                    <select id="manager_id" name="manager_id" class="form-select">
                        <option value="">Select Manager</option>
                        <?php foreach ($leaders as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $project['manager_id'] == $l['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($l['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="planning" <?= $project['status'] === 'planning' ? 'selected' : '' ?>>Planning</option>
                        <option value="active" <?= $project['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="on_hold" <?= $project['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                        <option value="completed" <?= $project['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="archived" <?= $project['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $project['start_date'] ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="deadline">Target Deadline</label>
                <input type="date" id="deadline" name="deadline" class="form-control" value="<?= $project['deadline'] ?>">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/projects/view?id=<?= $project['id'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
