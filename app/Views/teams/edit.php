<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <a href="/teams/view?id=<?= $team['id'] ?>" style="font-size: 0.85rem; color: var(--primary);">← Back to Team</a>
        <h1 class="page-title" style="margin-top: 0.25rem;">Edit Team: <?= Security::escape($team['name']) ?></h1>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form action="/teams/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="id" value="<?= $team['id'] ?>">

            <div class="form-group">
                <label class="form-label" for="name">Team Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= Security::escape($team['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= Security::escape($team['description']) ?></textarea>
            </div>

            <?php if ($user['role'] === 'admin'): ?>
                <div class="form-group">
                    <label class="form-label" for="leader_id">Team Leader</label>
                    <select id="leader_id" name="leader_id" class="form-select">
                        <option value="">Select Team Leader</option>
                        <?php foreach ($leaders as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $team['leader_id'] == $l['id'] ? 'selected' : '' ?>>
                                <?= Security::escape($l['name']) ?> (<?= Security::escape($l['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/teams/view?id=<?= $team['id'] ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
