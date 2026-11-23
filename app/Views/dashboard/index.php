<?php
use App\Helpers\Security;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Welcome back, <?= Security::escape($user['name']) ?></h1>
        <p class="page-subtitle">Here is the latest progress across your teams and tasks.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if ($user['role'] !== 'team_member'): ?>
            <a href="/tasks/create" class="btn btn-primary">+ New Task</a>
            <a href="/projects/create" class="btn btn-secondary">+ New Project</a>
        <?php endif; ?>
    </div>
</div>

<!-- Metrics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">📋</div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$metrics['total_tasks'] ?></div>
            <div class="stat-label">Total Tasks</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info">⚡</div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$metrics['in_progress_tasks'] ?></div>
            <div class="stat-label">In Progress</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">✓</div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$metrics['completed_tasks'] ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">⚠</div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$metrics['overdue_tasks'] ?></div>
            <div class="stat-label">Overdue Tasks</div>
        </div>
    </div>
</div>

<!-- Two Column Dashboard Layout -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Active Projects & Recent Tasks -->
    <div>
        <!-- Active Projects -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Active Projects</h2>
                <a href="/projects" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">View All →</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Team</th>
                                <th>Progress</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No active projects.</td></tr>
                            <?php else: ?>
                                <?php foreach ($projects as $proj): ?>
                                    <tr>
                                        <td>
                                            <a href="/projects/view?id=<?= $proj['id'] ?>" style="font-weight: 600; color: var(--primary);">
                                                <?= Security::escape($proj['name']) ?>
                                            </a>
                                        </td>
                                        <td><?= Security::escape($proj['team_name']) ?></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; max-width: 100px;">
                                                    <div style="height: 100%; width: <?= $proj['progress'] ?>%; background: var(--primary);"></div>
                                                </div>
                                                <span style="font-size: 0.8rem; font-weight: 600;"><?= $proj['progress'] ?>%</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-<?= $proj['status'] ?>"><?= Security::escape($proj['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Tasks</h2>
                <a href="/tasks" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">View All Tasks →</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assignee</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTasks)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No recent tasks found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentTasks as $task): ?>
                                    <tr>
                                        <td>
                                            <a href="/tasks/view?id=<?= $task['id'] ?>" style="font-weight: 600; color: var(--text-main);">
                                                <?= Security::escape($task['title']) ?>
                                            </a>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= Security::escape($task['project_name']) ?></div>
                                        </td>
                                        <td><span class="badge badge-<?= $task['priority'] ?>"><?= Security::escape($task['priority']) ?></span></td>
                                        <td><span class="badge badge-<?= $task['status'] ?>"><?= Security::escape(str_replace('_', ' ', $task['status'])) ?></span></td>
                                        <td><?= Security::escape($task['assigned_name'] ?? 'Unassigned') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown & Activity Feed -->
    <div>
        <!-- Task Status Breakdown -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Status Breakdown</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php 
                    $statusMap = [
                        'to_do' => ['label' => 'To Do', 'color' => '#64748b'],
                        'in_progress' => ['label' => 'In Progress', 'color' => '#4f46e5'],
                        'review' => ['label' => 'Under Review', 'color' => '#f59e0b'],
                        'completed' => ['label' => 'Completed', 'color' => '#10b981'],
                        'blocked' => ['label' => 'Blocked', 'color' => '#ef4444'],
                    ];
                    $total = max(1, $metrics['total_tasks']);
                    foreach ($statusMap as $key => $meta): 
                        $count = $metrics['status_distribution'][$key] ?? 0;
                        $pct = round(($count / $total) * 100);
                    ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <span><?= $meta['label'] ?></span>
                                <span style="font-weight: 600;"><?= $count ?> (<?= $pct ?>%)</span>
                            </div>
                            <div style="height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden;">
                                <div style="height: 100%; width: <?= $pct ?>%; background: <?= $meta['color'] ?>;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Activity</h2>
                <a href="/activity" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Feed →</a>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if (empty($activities)): ?>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">No activity logged yet.</div>
                    <?php else: ?>
                        <?php foreach ($activities as $act): ?>
                            <div style="display: flex; gap: 0.75rem; font-size: 0.85rem;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); margin-top: 6px;"></div>
                                <div>
                                    <div><?= Security::escape($act['description']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 2px;">
                                        <?= date('M d, H:i', strtotime($act['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
