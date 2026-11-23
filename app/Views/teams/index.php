<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Teams</h1>
        <p class="page-subtitle">Collaborate with department units, functional squads, and project groups.</p>
    </div>
    <?php if ($user['role'] === 'admin'): ?>
        <a href="/teams/create" class="btn btn-primary">+ Create Team</a>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
    <?php if (empty($teams)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <div style="color: var(--text-muted); font-size: 1rem;">No teams found.</div>
        </div>
    <?php else: ?>
        <?php foreach ($teams as $team): ?>
            <div class="card" style="margin: 0; display: flex; flex-direction: column;">
                <div class="card-header">
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 700;">
                            <a href="/teams/view?id=<?= $team['id'] ?>" style="color: var(--text-main);">
                                <?= Security::escape($team['name']) ?>
                            </a>
                        </h2>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                            Lead: <b><?= Security::escape($team['leader_name'] ?? 'Unassigned') ?></b>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                        <?= Security::escape($team['description'] ?: 'No description provided.') ?>
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-light); padding-top: 0.75rem; font-size: 0.825rem; color: var(--text-muted);">
                        <span>👥 <b><?= $team['member_count'] ?></b> Members</span>
                        <span>📁 <b><?= $team['project_count'] ?></b> Projects</span>
                        <span>✓ <b><?= $team['task_count'] ?></b> Tasks</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
