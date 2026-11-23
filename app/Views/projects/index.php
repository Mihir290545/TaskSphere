<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">Manage project roadmaps, team assignments, and tracking metrics.</p>
    </div>
    <?php if ($user['role'] !== 'team_member'): ?>
        <a href="/projects/create" class="btn btn-primary">+ Create Project</a>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
    <?php if (empty($projects)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <div style="color: var(--text-muted); font-size: 1rem;">No projects available.</div>
        </div>
    <?php else: ?>
        <?php foreach ($projects as $proj): ?>
            <div class="card" style="margin: 0; display: flex; flex-direction: column;">
                <div class="card-header" style="align-items: flex-start;">
                    <div>
                        <span class="badge badge-<?= $proj['status'] ?>" style="margin-bottom: 0.5rem;"><?= Security::escape($proj['status']) ?></span>
                        <h2 style="font-size: 1.15rem; font-weight: 700;">
                            <a href="/projects/view?id=<?= $proj['id'] ?>" style="color: var(--text-main);">
                                <?= Security::escape($proj['name']) ?>
                            </a>
                        </h2>
                    </div>
                </div>
                <div class="card-body" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                        <?= Security::escape($proj['description'] ? substr($proj['description'], 0, 120) . '...' : 'No description.') ?>
                    </p>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--text-muted);">Progress (<?= $proj['completed_tasks'] ?> / <?= $proj['total_tasks'] ?> tasks)</span>
                            <span style="font-weight: 700;"><?= $proj['progress'] ?>%</span>
                        </div>
                        <div style="height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-bottom: 1rem;">
                            <div style="height: 100%; width: <?= $proj['progress'] ?>%; background: var(--primary);"></div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-light); padding-top: 0.75rem; font-size: 0.8rem; color: var(--text-muted);">
                            <span>Team: <b><?= Security::escape($proj['team_name']) ?></b></span>
                            <span>Due: <b><?= $proj['deadline'] ? date('M d, Y', strtotime($proj['deadline'])) : 'Open' ?></b></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
