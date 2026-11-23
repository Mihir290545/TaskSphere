<?php
use App\Helpers\Security;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Kanban Workflow Board</h1>
        <p class="page-subtitle">Drag and drop task cards to instantly update status in real-time.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/tasks" class="btn btn-secondary">List View</a>
        <?php if ($user['role'] !== 'team_member'): ?>
            <a href="/tasks/create" class="btn btn-primary">+ Create Task</a>
        <?php endif; ?>
    </div>
</div>

<div class="kanban-board">
    <?php foreach ($columns as $statusKey => $col): ?>
        <div class="kanban-col" data-status="<?= $statusKey ?>">
            <div class="kanban-header">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span><?= $col['title'] ?></span>
                    <span class="badge badge-<?= $col['badge'] ?>"><?= count($col['tasks']) ?></span>
                </div>
            </div>
            <div class="kanban-cards">
                <?php foreach ($col['tasks'] as $task): ?>
                    <div class="kanban-card" data-id="<?= $task['id'] ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-light);">#<?= $task['id'] ?></span>
                            <span class="badge badge-<?= $task['priority'] ?>"><?= Security::escape($task['priority']) ?></span>
                        </div>
                        <a href="/tasks/view?id=<?= $task['id'] ?>" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 0.35rem;">
                            <?= Security::escape($task['title']) ?>
                        </a>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                            <?= Security::escape($task['project_name']) ?>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-light); padding-top: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">
                            <span><?= Security::escape($task['assigned_name'] ?? 'Unassigned') ?></span>
                            <?php if ($task['due_date']): ?>
                                <span><?= date('M d', strtotime($task['due_date'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
