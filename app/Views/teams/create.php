<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/teams" style="font-size: 0.85rem; color: var(--primary);">← Back to Teams</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Create New Team</h1>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form action="/teams/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label class="form-label" for="name">Team Name *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Mobile Engineering Squad" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Department purpose, responsibility area..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="leader_id">Team Leader</label>
                <select id="leader_id" name="leader_id" class="form-select">
                    <option value="">Select Team Leader</option>
                    <?php foreach ($leaders as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= Security::escape($l['name']) ?> (<?= Security::escape($l['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Create Team</button>
                <a href="/teams" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
