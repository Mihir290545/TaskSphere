<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <a href="/tasks" style="font-size: 0.85rem; color: var(--primary);">← Back to Tasks</a>
            <span class="badge badge-<?= $task['status'] ?>"><?= Security::escape(str_replace('_', ' ', $task['status'])) ?></span>
            <span class="badge badge-<?= $task['priority'] ?>"><?= Security::escape($task['priority']) ?></span>
        </div>
        <h1 class="page-title"><?= Security::escape($task['title']) ?></h1>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/tasks/edit?id=<?= $task['id'] ?>" class="btn btn-secondary">Edit Task</a>
        <?php if ($user['role'] !== 'team_member'): ?>
            <form action="/tasks/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Column: Description & Comments -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Description</h2>
            </div>
            <div class="card-body">
                <div style="white-space: pre-line; color: var(--text-main); font-size: 0.95rem; line-height: 1.6;">
                    <?= Security::escape($task['description'] ?: 'No description provided.') ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Discussion & Comments (<?= count($comments) ?>)</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2rem;">
                    <?php if (empty($comments)): ?>
                        <div style="color: var(--text-muted); font-size: 0.9rem;">No comments yet. Start the conversation!</div>
                    <?php else: ?>
                        <?php foreach ($comments as $comm): ?>
                            <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-light); padding-bottom: 1rem;">
                                <div class="user-avatar-sm" style="flex-shrink: 0;">
                                    <?= strtoupper(substr($comm['user_name'], 0, 1)) ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                        <div>
                                            <strong style="font-size: 0.9rem;"><?= Security::escape($comm['user_name']) ?></strong>
                                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">
                                                <?= date('M d, Y h:i A', strtotime($comm['created_at'])) ?>
                                            </span>
                                        </div>
                                        <?php if ($comm['user_id'] == $user['id'] || $user['role'] === 'admin'): ?>
                                            <form action="/tasks/comment/delete" method="POST" onsubmit="return confirm('Delete this comment?');" style="margin: 0;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="comment_id" value="<?= $comm['id'] ?>">
                                                <button type="submit" style="background: none; border: none; color: var(--danger); font-size: 0.75rem; cursor: pointer;">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.9rem; color: var(--text-main); white-space: pre-line;">
                                        <?= Security::escape($comm['comment']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Comment Form -->
                <form action="/tasks/comment" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <div class="form-group">
                        <label class="form-label">Add a comment</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Share updates or feedback..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info Column -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Task Details</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Project</div>
                        <div style="font-weight: 600; margin-top: 2px;">
                            <a href="/projects/view?id=<?= $task['project_id'] ?>" style="color: var(--primary);">
                                <?= Security::escape($task['project_name']) ?>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Team</div>
                        <div style="font-weight: 600; margin-top: 2px;">
                            <a href="/teams/view?id=<?= $task['team_id'] ?>" style="color: var(--primary);">
                                <?= Security::escape($task['team_name']) ?>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Assignee</div>
                        <div style="font-weight: 600; margin-top: 2px;">
                            <?= Security::escape($task['assigned_name'] ?? 'Unassigned') ?>
                        </div>
                    </div>

                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Created By</div>
                        <div style="margin-top: 2px;">
                            <?= Security::escape($task['creator_name']) ?>
                        </div>
                    </div>

                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Due Date</div>
                        <div style="font-weight: 600; margin-top: 2px;">
                            <?= $task['due_date'] ? date('F d, Y', strtotime($task['due_date'])) : 'No due date' ?>
                        </div>
                    </div>

                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Created At</div>
                        <div style="margin-top: 2px; color: var(--text-muted);">
                            <?= date('M d, Y H:i', strtotime($task['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
