<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;

class ActivityController extends Controller {
    public function index(): void {
        $user = $this->user();
        $activities = ActivityLog::getRecent(50);

        $this->render('activity/index', [
            'title' => 'Activity Feed & Audit Log - TaskFlow',
            'activities' => $activities,
            'user' => $user
        ]);
    }
}
