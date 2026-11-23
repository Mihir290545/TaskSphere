<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use App\Models\ActivityLog;

class ProjectController extends Controller {
    public function index(): void {
        $user = $this->user();
        $projects = Project::getAll($user['id'], $user['role']);

        $this->render('projects/index', [
            'title' => 'Projects - TaskFlow',
            'projects' => $projects,
            'user' => $user
        ]);
    }

    public function view(): void {
        $id = (int)Request::get('id', 0);
        $project = Project::findById($id);

        if (!$project) {
            Session::flash('error', 'Project not found.');
            $this->redirect('/projects');
        }

        $user = $this->user();
        $tasks = Task::getAll(['project_id' => $id], $user['id'], $user['role']);

        $this->render('projects/view', [
            'title' => "Project: {$project['name']} - TaskFlow",
            'project' => $project,
            'tasks' => $tasks,
            'user' => $user
        ]);
    }

    public function create(): void {
        $user = $this->user();
        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Only leaders and admins can create projects.');
            $this->redirect('/projects');
        }

        $teams = Team::getAll($user['id'], $user['role']);
        $leaders = User::getTeamLeaders();

        $this->render('projects/create', [
            'title' => 'New Project - TaskFlow',
            'teams' => $teams,
            'leaders' => $leaders,
            'user' => $user
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        $user = $this->user();

        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/projects');
        }

        $name = trim((string)Request::post('name', ''));
        $description = trim((string)Request::post('description', ''));
        $teamId = (int)Request::post('team_id', 0);
        $managerId = (int)Request::post('manager_id', 0) ?: $user['id'];
        $status = Request::post('status', 'planning');
        $startDate = Request::post('start_date') ?: null;
        $deadline = Request::post('deadline') ?: null;

        if (empty($name) || !$teamId) {
            Session::flash('error', 'Project name and team are required.');
            $this->redirect('/projects/create');
        }

        $projectId = Project::create([
            'name' => $name,
            'description' => $description,
            'team_id' => $teamId,
            'manager_id' => $managerId,
            'status' => $status,
            'start_date' => $startDate,
            'deadline' => $deadline
        ]);

        ActivityLog::log($user['id'], 'create', 'project', $projectId, "{$user['name']} created project \"{$name}\"");

        Session::flash('success', 'Project created successfully.');
        $this->redirect('/projects');
    }

    public function edit(): void {
        $id = (int)Request::get('id', 0);
        $project = Project::findById($id);

        if (!$project) {
            Session::flash('error', 'Project not found.');
            $this->redirect('/projects');
        }

        $user = $this->user();
        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/projects');
        }

        $teams = Team::getAll($user['id'], $user['role']);
        $leaders = User::getTeamLeaders();

        $this->render('projects/edit', [
            'title' => 'Edit Project - TaskFlow',
            'project' => $project,
            'teams' => $teams,
            'leaders' => $leaders,
            'user' => $user
        ]);
    }

    public function update(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $project = Project::findById($id);

        if (!$project) {
            Session::flash('error', 'Project not found.');
            $this->redirect('/projects');
        }

        $user = $this->user();
        if ($user['role'] === 'team_member') {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/projects');
        }

        $name = trim((string)Request::post('name', ''));
        $description = trim((string)Request::post('description', ''));
        $teamId = (int)Request::post('team_id', 0);
        $managerId = (int)Request::post('manager_id', 0) ?: $user['id'];
        $status = Request::post('status', 'planning');
        $startDate = Request::post('start_date') ?: null;
        $deadline = Request::post('deadline') ?: null;

        Project::update($id, [
            'name' => $name,
            'description' => $description,
            'team_id' => $teamId,
            'manager_id' => $managerId,
            'status' => $status,
            'start_date' => $startDate,
            'deadline' => $deadline
        ]);

        ActivityLog::log($user['id'], 'update', 'project', $id, "{$user['name']} updated project \"{$name}\"");

        Session::flash('success', 'Project updated successfully.');
        $this->redirect("/projects/view?id={$id}");
    }

    public function delete(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $user = $this->user();

        if ($user['role'] !== 'admin') {
            Session::flash('error', 'Only administrators can delete projects.');
            $this->redirect('/projects');
        }

        $project = Project::findById($id);
        if ($project) {
            Project::delete($id);
            ActivityLog::log($user['id'], 'delete', 'project', $id, "{$user['name']} deleted project \"{$project['name']}\"");
            Session::flash('success', 'Project deleted successfully.');
        }

        $this->redirect('/projects');
    }
}
