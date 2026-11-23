<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;
use App\Models\ActivityLog;

class ProfileController extends Controller {
    public function index(): void {
        $currentUser = $this->user();
        $user = User::findById($currentUser['id']);

        $this->render('profile/index', [
            'title' => 'My Profile - TaskFlow',
            'user' => $user
        ]);
    }

    public function update(): void {
        $this->validateCsrf();
        $currentUser = $this->user();
        $name = trim((string)Request::post('name', ''));

        if (empty($name)) {
            Session::flash('error', 'Name cannot be empty.');
            $this->redirect('/profile');
        }

        User::updateProfile($currentUser['id'], $name);
        
        // Update session
        $updated = User::findById($currentUser['id']);
        unset($updated['password']);
        Session::set('user', $updated);

        ActivityLog::log($currentUser['id'], 'update_profile', 'user', $currentUser['id'], "{$name} updated profile details.");

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }

    public function changePassword(): void {
        $this->validateCsrf();
        $currentUser = $this->user();
        $currentPassword = (string)Request::post('current_password', '');
        $newPassword = (string)Request::post('new_password', '');
        $confirmPassword = (string)Request::post('password_confirmation', '');

        $dbUser = User::findById($currentUser['id']);

        if (!password_verify($currentPassword, $dbUser['password'])) {
            Session::flash('error', 'Current password does not match.');
            $this->redirect('/profile');
        }

        if (strlen($newPassword) < 8) {
            Session::flash('error', 'New password must be at least 8 characters long.');
            $this->redirect('/profile');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'New passwords do not match.');
            $this->redirect('/profile');
        }

        User::updatePassword($currentUser['id'], $newPassword);
        ActivityLog::log($currentUser['id'], 'change_password', 'user', $currentUser['id'], "Password changed successfully.");

        Session::flash('success', 'Password changed successfully.');
        $this->redirect('/profile');
    }
}
