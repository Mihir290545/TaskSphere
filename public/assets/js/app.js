// TaskFlow Application JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Mobile Sidebar Toggle
    const mobileBtn = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });

    // Toast Notification Utility
    window.showToast = function(message, type = 'info') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // Kanban Drag and Drop Logic
    const kanbanCards = document.querySelectorAll('.kanban-card');
    const kanbanCols = document.querySelectorAll('.kanban-col');

    if (kanbanCards.length && kanbanCols.length) {
        let draggedCard = null;

        kanbanCards.forEach(card => {
            card.setAttribute('draggable', 'true');
            card.addEventListener('dragstart', (e) => {
                draggedCard = card;
                card.classList.add('dragging');
                e.dataTransfer.setData('text/plain', card.dataset.id);
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
                draggedCard = null;
            });
        });

        kanbanCols.forEach(col => {
            col.addEventListener('dragover', (e) => {
                e.preventDefault();
                col.style.background = '#e2e8f0';
            });

            col.addEventListener('dragleave', () => {
                col.style.background = '#f1f5f9';
            });

            col.addEventListener('drop', async (e) => {
                e.preventDefault();
                col.style.background = '#f1f5f9';
                if (!draggedCard) return;

                const cardsContainer = col.querySelector('.kanban-cards');
                const newStatus = col.dataset.status;
                const taskId = draggedCard.dataset.id;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Optimistic UI move
                cardsContainer.appendChild(draggedCard);

                // Update server via Fetch API
                try {
                    const formData = new FormData();
                    formData.append('id', taskId);
                    formData.append('status', newStatus);
                    formData.append('csrf_token', csrfToken);

                    const res = await fetch('/tasks/update-status-ajax', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    if (data.success) {
                        window.showToast(`Task status updated to ${newStatus.replace('_', ' ')}!`, 'success');
                    } else {
                        window.showToast(data.error || 'Failed to update status', 'error');
                        // Reload if failure to sync state
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } catch (err) {
                    window.showToast('Network error while updating status', 'error');
                }
            });
        });
    }
});
