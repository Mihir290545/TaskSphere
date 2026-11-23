<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Task;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\TaskComment;
use App\Models\Notification;
use App\Models\ActivityLog;

class TaskController extends Controller {
    public function index(): void {
        $user = $this->user();
        $filters = [
            'search' => Request::get('search', ''),
            'status' => Request::get('status', ''),
            'priority' => Request::get('priority', ''),
            'project_id' => Request::get('project_id', ''),
            'team_id' => Request::get('team_id', ''),
            'assigned_to' => Request::get('assigned_to', ''),
            'overdue' => Request::get('overdue', ''),
            'sort' => Request::get('sort', 'newest'),
        ];

        $tasks = Task::getAll($filters, $user['id'], $user['role']);
        $projects = Project::getAll($user['id'], $user['role']);
        $teams = Team::getAll($user['id'], $user['role']);
        $members = User::getActiveMembers();

        $this->render('tasks/index', [
            'title' => 'Tasks - TaskFlow',
            'tasks' => $tasks,
            'filters' => $filters,
            'projects' => $projects,
            'teams' => $teams,
            'members' => $members,
            'user' => $user
        ]);
    }

    public function kanban(): void {
        $user = $this->user();
        $allTasks = Task::getAll([], $user['id'], $user['role']);

        $columns = [
            'to_do' => ['title' => 'To Do', 'badge' => 'secondary', 'tasks' => []],
            'in_progress' => ['title' => 'In Progress', 'badge' => 'primary', 'tasks' => []],
            'review' => ['title' => 'Review', 'badge' => 'warning', 'tasks' => []],
            'completed' => ['title' => 'Completed', 'badge' => 'success', 'tasks' => []],
            'blocked' => ['title' => 'Blocked', 'badge' => 'danger', 'tasks' => []],
        ];

        foreach ($allTasks as $task) {
            $st = $task['status'];
            if (isset($columns[$st])) {
                $columns[$st]['tasks'][] = $task;
            }
        }

        $this->render('tasks/kanban', [
            'title' => 'Kanban Board - TaskFlow',
            'columns' => $columns,
            'user' => $user
        ]);
    }

    public function view(): void {
        $id = (int)Request::get('id', 0);
        $task = Task::findById($id);

        if (!$task) {
            Session::flash('error', 'Task not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        // Permission check
        if ($user['role'] === 'team_member') {
            $isMember = Team::isMember((int)$task['team_id'], (int)$user['id']);
            if (!$isMember && $task['assigned_to'] != $user['id']) {
                Session::flash('error', 'Access denied to this task.');
                $this->redirect('/tasks');
            }
        }

        $comments = TaskComment::getByTaskId($id);

        $this->render('tasks/view', [
            'title' => "Task #{$task['id']}: {$task['title']} - TaskFlow",
            'task' => $task,
            'comments' => $comments,
            'user' => $user
        ]);
    }

    public function create(): void {
        $user = $this->user();
        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Only team leaders and administrators can create tasks.');
            $this->redirect('/tasks');
        }

        $projects = Project::getAll($user['id'], $user['role']);
        $teams = Team::getAll($user['id'], $user['role']);
        $members = User::getActiveMembers();

        $this->render('tasks/create', [
            'title' => 'Create Task - TaskFlow',
            'projects' => $projects,
            'teams' => $teams,
            'members' => $members,
            'user' => $user
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        $user = $this->user();

        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/tasks');
        }

        $title = trim((string)Request::post('title', ''));
        $description = trim((string)Request::post('description', ''));
        $projectId = (int)Request::post('project_id', 0);
        $teamId = (int)Request::post('team_id', 0);
        $assignedTo = (int)Request::post('assigned_to', 0);
        $priority = Request::post('priority', 'medium');
        $status = Request::post('status', 'to_do');
        $startDate = Request::post('start_date') ?: null;
        $dueDate = Request::post('due_date') ?: null;

        if (empty($title) || !$projectId || !$teamId) {
            Session::flash('error', 'Task title, project, and team are required.');
            $this->redirect('/tasks/create');
        }

        // Leader check: leader must own team
        if ($user['role'] === 'team_leader') {
            if (!Team::isLeader($teamId, $user['id']) && !Team::isMember($teamId, $user['id'])) {
                Session::flash('error', 'You can only create tasks for your assigned teams.');
                $this->redirect('/tasks/create');
            }
        }

        $taskId = Task::create([
            'title' => $title,
            'description' => $description,
            'project_id' => $projectId,
            'team_id' => $teamId,
            'assigned_to' => $assignedTo ?: null,
            'created_by' => $user['id'],
            'priority' => $priority,
            'status' => $status,
            'start_date' => $startDate,
            'due_date' => $dueDate,
        ]);

        ActivityLog::log($user['id'], 'create', 'task', $taskId, "{$user['name']} created task \"{$title}\"");

        if ($assignedTo && $assignedTo != $user['id']) {
            Notification::create(
                $assignedTo,
                'Task Assigned',
                "{$user['name']} assigned you task: {$title}",
                "/tasks/view?id={$taskId}"
            );
        }

        Session::flash('success', 'Task created successfully.');
        $this->redirect('/tasks');
    }

    public function edit(): void {
        $id = (int)Request::get('id', 0);
        $task = Task::findById($id);

        if (!$task) {
            Session::flash('error', 'Task not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        if ($user['role'] === 'team_member' && $task['assigned_to'] != $user['id']) {
            Session::flash('error', 'You do not have permission to edit this task.');
            $this->redirect('/tasks');
        }

        $projects = Project::getAll($user['id'], $user['role']);
        $teams = Team::getAll($user['id'], $user['role']);
        $members = User::getActiveMembers();

        $this->render('tasks/edit', [
            'title' => 'Edit Task - TaskFlow',
            'task' => $task,
            'projects' => $projects,
            'teams' => $teams,
            'members' => $members,
            'user' => $user
        ]);
    }

    public function update(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $task = Task::findById($id);

        if (!$task) {
            Session::flash('error', 'Task not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        $isMember = ($user['role'] === 'team_member');

        $title = $isMember ? $task['title'] : trim((string)Request::post('title', $task['title']));
        $description = trim((string)Request::post('description', $task['description']));
        $projectId = $isMember ? (int)$task['project_id'] : (int)Request::post('project_id', $task['project_id']);
        $teamId = $isMember ? (int)$task['team_id'] : (int)Request::post('team_id', $task['team_id']);
        $assignedTo = $isMember ? $task['assigned_to'] : (int)Request::post('assigned_to', 0);
        $priority = $isMember ? $task['priority'] : Request::post('priority', $task['priority']);
        $status = Request::post('status', $task['status']);
        $startDate = $isMember ? $task['start_date'] : (Request::post('start_date') ?: null);
        $dueDate = $isMember ? $task['due_date'] : (Request::post('due_date') ?: null);

        Task::update($id, [
            'title' => $title,
            'description' => $description,
            'project_id' => $projectId,
            'team_id' => $teamId,
            'assigned_to' => $assignedTo ?: null,
            'priority' => $priority,
            'status' => $status,
            'start_date' => $startDate,
            'due_date' => $dueDate,
        ]);

        ActivityLog::log($user['id'], 'update', 'task', $id, "{$user['name']} updated task \"{$title}\" (Status: {$status})");

        if ($assignedTo && $assignedTo != $task['assigned_to'] && $assignedTo != $user['id']) {
            Notification::create(
                $assignedTo,
                'Task Reassigned',
                "{$user['name']} assigned you task: {$title}",
                "/tasks/view?id={$id}"
            );
        }

        Session::flash('success', 'Task updated successfully.');
        $this->redirect("/tasks/view?id={$id}");
    }

    public function updateStatusAjax(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $status = Request::post('status', '');

        $allowed = ['to_do', 'in_progress', 'review', 'completed', 'blocked'];
        if (!in_array($status, $allowed)) {
            $this->json(['success' => false, 'error' => 'Invalid status value.'], 400);
        }

        $task = Task::findById($id);
        if (!$task) {
            $this->json(['success' => false, 'error' => 'Task not found.'], 404);
        }

        $user = $this->user();
        if ($user['role'] === 'team_member') {
            if ($task['assigned_to'] != $user['id'] && !Team::isMember((int)$task['team_id'], (int)$user['id'])) {
                $this->json(['success' => false, 'error' => 'Unauthorized action.'], 403);
            }
        }

        Task::updateStatus($id, $status);
        ActivityLog::log($user['id'], 'update_status', 'task', $id, "{$user['name']} changed status of \"{$task['title']}\" to {$status}");

        // Notify creator if updater is different
        if ($task['created_by'] != $user['id']) {
            Notification::create(
                (int)$task['created_by'],
                'Task Status Update',
                "{$user['name']} changed status of task \"{$task['title']}\" to " . strtoupper(str_replace('_', ' ', $status)),
                "/tasks/view?id={$id}"
            );
        }

        $this->json(['success' => true, 'message' => 'Status updated successfully.', 'status' => $status]);
    }

    public function delete(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $task = Task::findById($id);

        if (!$task) {
            Session::flash('error', 'Task not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Team members cannot delete tasks.');
            $this->redirect('/tasks');
        }

        Task::delete($id);
        ActivityLog::log($user['id'], 'delete', 'task', $id, "{$user['name']} deleted task \"{$task['title']}\"");

        Session::flash('success', 'Task deleted successfully.');
        $this->redirect('/tasks');
    }

    public function addComment(): void {
        $this->validateCsrf();
        $taskId = (int)Request::post('task_id', 0);
        $commentText = trim((string)Request::post('comment', ''));

        if (empty($commentText) || !$taskId) {
            Session::flash('error', 'Comment cannot be empty.');
            $this->redirect("/tasks/view?id={$taskId}");
        }

        $task = Task::findById($taskId);
        if (!$task) {
            Session::flash('error', 'Task not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        $commentId = TaskComment::create($taskId, $user['id'], $commentText);
        ActivityLog::log($user['id'], 'comment', 'task', $taskId, "{$user['name']} commented on task \"{$task['title']}\"");

        // Notify assigned user if commenter is someone else
        if ($task['assigned_to'] && $task['assigned_to'] != $user['id']) {
            Notification::create(
                (int)$task['assigned_to'],
                'New Comment on Task',
                "{$user['name']} commented on task \"{$task['title']}\"",
                "/tasks/view?id={$taskId}"
            );
        }

        Session::flash('success', 'Comment added.');
        $this->redirect("/tasks/view?id={$taskId}");
    }

    public function deleteComment(): void {
        $this->validateCsrf();
        $commentId = (int)Request::post('comment_id', 0);
        $comment = TaskComment::findById($commentId);

        if (!$comment) {
            Session::flash('error', 'Comment not found.');
            $this->redirect('/tasks');
        }

        $user = $this->user();
        if ($comment['user_id'] != $user['id'] && $user['role'] !== 'admin') {
            Session::flash('error', 'You cannot delete this comment.');
            $this->redirect("/tasks/view?id={$comment['task_id']}");
        }

        TaskComment::delete($commentId);
        Session::flash('success', 'Comment removed.');
        $this->redirect("/tasks/view?id={$comment['task_id']}");
    }
}
