<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/projects" style="font-size: 0.85rem; color: var(--primary);">← Back to Projects</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Create New Project</h1>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="/projects/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label" for="name">Project Name *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Mobile Client App 2.0" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Scope, milestones, and deliverables..."></textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="team_id">Assigned Team *</label>
                    <select id="team_id" name="team_id" class="form-select" required>
                        <option value="">Select Team</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= Security::escape($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="manager_id">Project Manager</label>
                    <select id="manager_id" name="manager_id" class="form-select">
                        <option value="">Select Manager</option>
                        <?php foreach ($leaders as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $l['id'] == $user['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($l['name']) ?> (<?= Security::escape($l['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label" for="status">Initial Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="planning" selected>Planning</option>
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="deadline">Target Deadline</label>
                <input type="date" id="deadline" name="deadline" class="form-control">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Create Project</button>
                <a href="/projects" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
