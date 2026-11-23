<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Team;
use App\Models\ActivityLog;
use App\Models\Notification;

class DashboardController extends Controller {
    public function index(): void {
        $user = $this->user();
        $metrics = Task::getDashboardMetrics($user['id'], $user['role']);

        // Recent tasks for this user/team
        $recentTasks = Task::getAll([], $user['id'], $user['role']);
        $recentTasks = array_slice($recentTasks, 0, 5);

        // Upcoming deadlines
        $upcomingTasks = array_filter($recentTasks, function($t) {
            return !empty($t['due_date']) && $t['status'] !== 'completed';
        });

        // Recent activities
        $activities = ActivityLog::getRecent(8);

        // Active projects
        $projects = Project::getAll($user['id'], $user['role']);
        $projects = array_slice($projects, 0, 4);

        $this->render('dashboard/index', [
            'title' => 'Dashboard - TaskFlow',
            'user' => $user,
            'metrics' => $metrics,
            'recentTasks' => $recentTasks,
            'upcomingTasks' => $upcomingTasks,
            'activities' => $activities,
            'projects' => $projects,
        ]);
    }
}
