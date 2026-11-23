<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Team;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\ActivityLog;

class TeamController extends Controller {
    public function index(): void {
        $user = $this->user();
        $teams = Team::getAll($user['id'], $user['role']);

        $this->render('teams/index', [
            'title' => 'Teams - TaskFlow',
            'teams' => $teams,
            'user' => $user
        ]);
    }

    public function view(): void {
        $id = (int)Request::get('id', 0);
        $team = Team::findById($id);

        if (!$team) {
            Session::flash('error', 'Team not found.');
            $this->redirect('/teams');
        }

        $user = $this->user();
        $members = Team::getMembers($id);
        $allUsers = User::getActiveMembers();
        $tasks = Task::getAll(['team_id' => $id], $user['id'], $user['role']);
        $projects = Project::getAll($user['id'], $user['role']);
        $teamProjects = array_filter($projects, fn($p) => $p['team_id'] == $id);

        $this->render('teams/view', [
            'title' => "Team: {$team['name']} - TaskFlow",
            'team' => $team,
            'members' => $members,
            'allUsers' => $allUsers,
            'tasks' => $tasks,
            'projects' => $teamProjects,
            'user' => $user
        ]);
    }

    public function create(): void {
        $user = $this->user();
        if ($user['role'] !== 'admin') {
            Session::flash('error', 'Only administrators can create teams.');
            $this->redirect('/teams');
        }

        $leaders = User::getTeamLeaders();

        $this->render('teams/create', [
            'title' => 'Create Team - TaskFlow',
            'leaders' => $leaders,
            'user' => $user
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        $user = $this->user();

        if ($user['role'] !== 'admin') {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/teams');
        }

        $name = trim((string)Request::post('name', ''));
        $description = trim((string)Request::post('description', ''));
        $leaderId = (int)Request::post('leader_id', 0) ?: null;

        if (empty($name)) {
            Session::flash('error', 'Team name is required.');
            $this->redirect('/teams/create');
        }

        $teamId = Team::create($name, $description, $leaderId);
        ActivityLog::log($user['id'], 'create', 'team', $teamId, "{$user['name']} created team \"{$name}\"");

        Session::flash('success', 'Team created successfully.');
        $this->redirect('/teams');
    }

    public function edit(): void {
        $id = (int)Request::get('id', 0);
        $team = Team::findById($id);

        if (!$team) {
            Session::flash('error', 'Team not found.');
            $this->redirect('/teams');
        }

        $user = $this->user();
        if ($user['role'] !== 'admin' && !($user['role'] === 'team_leader' && $team['leader_id'] == $user['id'])) {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/teams');
        }

        $leaders = User::getTeamLeaders();

        $this->render('teams/edit', [
            'title' => 'Edit Team - TaskFlow',
            'team' => $team,
            'leaders' => $leaders,
            'user' => $user
        ]);
    }

    public function update(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $team = Team::findById($id);

        if (!$team) {
            Session::flash('error', 'Team not found.');
            $this->redirect('/teams');
        }

        $user = $this->user();
        if ($user['role'] !== 'admin' && !($user['role'] === 'team_leader' && $team['leader_id'] == $user['id'])) {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect('/teams');
        }

        $name = trim((string)Request::post('name', ''));
        $description = trim((string)Request::post('description', ''));
        $leaderId = ($user['role'] === 'admin') ? ((int)Request::post('leader_id', 0) ?: null) : $team['leader_id'];

        Team::update($id, $name, $description, $leaderId);
        ActivityLog::log($user['id'], 'update', 'team', $id, "{$user['name']} updated team \"{$name}\"");

        Session::flash('success', 'Team updated successfully.');
        $this->redirect("/teams/view?id={$id}");
    }

    public function delete(): void {
        $this->validateCsrf();
        $id = (int)Request::post('id', 0);
        $user = $this->user();

        if ($user['role'] !== 'admin') {
            Session::flash('error', 'Only administrators can delete teams.');
            $this->redirect('/teams');
        }

        $team = Team::findById($id);
        if ($team) {
            Team::delete($id);
            ActivityLog::log($user['id'], 'delete', 'team', $id, "{$user['name']} deleted team \"{$team['name']}\"");
            Session::flash('success', 'Team deleted.');
        }

        $this->redirect('/teams');
    }

    public function addMember(): void {
        $this->validateCsrf();
        $teamId = (int)Request::post('team_id', 0);
        $userId = (int)Request::post('user_id', 0);
        $user = $this->user();

        $team = Team::findById($teamId);
        if (!$team) {
            Session::flash('error', 'Team not found.');
            $this->redirect('/teams');
        }

        if ($user['role'] !== 'admin' && !($user['role'] === 'team_leader' && $team['leader_id'] == $user['id'])) {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect("/teams/view?id={$teamId}");
        }

        Team::addMember($teamId, $userId);
        $targetUser = User::findById($userId);
        ActivityLog::log($user['id'], 'add_member', 'team', $teamId, "Added {$targetUser['name']} to {$team['name']}");

        Session::flash('success', 'Member added to team.');
        $this->redirect("/teams/view?id={$teamId}");
    }

    public function removeMember(): void {
        $this->validateCsrf();
        $teamId = (int)Request::post('team_id', 0);
        $userId = (int)Request::post('user_id', 0);
        $user = $this->user();

        $team = Team::findById($teamId);
        if (!$team) {
            Session::flash('error', 'Team not found.');
            $this->redirect('/teams');
        }

        if ($user['role'] !== 'admin' && !($user['role'] === 'team_leader' && $team['leader_id'] == $user['id'])) {
            Session::flash('error', 'Unauthorized action.');
            $this->redirect("/teams/view?id={$teamId}");
        }

        Team::removeMember($teamId, $userId);
        $targetUser = User::findById($userId);
        ActivityLog::log($user['id'], 'remove_member', 'team', $teamId, "Removed {$targetUser['name']} from {$team['name']}");

        Session::flash('success', 'Member removed from team.');
        $this->redirect("/teams/view?id={$teamId}");
    }
}
