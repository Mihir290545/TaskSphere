<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Notification;
use App\Models\ActivityLog;

class NotificationController extends Controller {
    public function index(): void {
        $user = $this->user();
        $notifications = Notification::getForUser($user['id'], 50);

        $this->render('notifications/index', [
            'title' => 'Notifications - TaskFlow',
            'notifications' => $notifications,
            'user' => $user
        ]);
    }

    public function markAsRead(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $user = $this->user();

        Notification::markAsRead($id, $user['id']);

        if (Request::isAjax()) {
            $this->json(['success' => true]);
        }
        $this->redirect('/notifications');
    }

    public function markAllRead(): void {
        $this->validateCsrf();
        $user = $this->user();

        Notification::markAllAsRead($user['id']);

        if (Request::isAjax()) {
            $this->json(['success' => true]);
        }
        Session::flash('success', 'All notifications marked as read.');
        $this->redirect('/notifications');
    }
}
