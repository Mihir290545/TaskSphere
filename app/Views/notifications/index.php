<?php
use App\Helpers\Security;
$csrfToken = Security::generateCsrf();
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle">Alerts, assignments, and updates on your tasks and team activities.</p>
    </div>
    <form action="/notifications/read-all" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <button type="submit" class="btn btn-secondary btn-sm">Mark All as Read</button>
    </form>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div style="display: flex; flex-direction: column;">
            <?php if (empty($notifications)): ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    You're all caught up! No notifications.
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-light); background: <?= $notif['is_read'] ? '#ffffff' : '#f8fafc' ?>;">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: <?= $notif['is_read'] ? '#cbd5e1' : 'var(--primary)' ?>; margin-top: 6px;"></div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.25rem;">
                                    <?= Security::escape($notif['title']) ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.35rem;">
                                    <?= Security::escape($notif['message']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-light);">
                                    <?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <?php if (!empty($notif['link'])): ?>
                                <a href="<?= Security::escape($notif['link']) ?>" class="btn btn-secondary btn-sm">Open</a>
                            <?php endif; ?>
                            <?php if (!$notif['is_read']): ?>
                                <form action="/notifications/read" method="POST" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="id" value="<?= $notif['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm">Mark Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
