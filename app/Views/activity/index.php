<?php
use App\Helpers\Security;
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Activity Feed & Audit Log</h1>
        <p class="page-subtitle">Chronological record of key events, task movements, and administrative changes.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php if (empty($activities)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No system activities recorded.</div>
            <?php else: ?>
                <?php foreach ($activities as $act): ?>
                    <div style="display: flex; gap: 1rem; align-items: flex-start; border-bottom: 1px solid var(--border-light); padding-bottom: 1rem;">
                        <div class="user-avatar-sm" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                            <?= strtoupper(substr($act['user_name'] ?? 'S', 0, 1)) ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.9rem; color: var(--text-main);">
                                <strong><?= Security::escape($act['user_name'] ?? 'System') ?></strong>
                                <span><?= Security::escape($act['description']) ?></span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 0.25rem;">
                                <?= date('M d, Y h:i A', strtotime($act['created_at'])) ?> • Action: <code><?= Security::escape($act['action']) ?></code>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
