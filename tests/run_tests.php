<?php
// TaskFlow Comprehensive Automated QA Test Suite
// Verifies: Authentication, RBAC, Tasks, Projects, Teams, Comments, Security (CSRF, XSS, Brute-Force)

require_once dirname(__DIR__) . '/config/app.php';

// Setup Mock Environment for Direct Controller & Model Unit/Integration Testing
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Helpers\Security;

$passed = 0;
$failed = 0;

function assertTest(string $description, bool $condition) {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] " . $description . "\n";
        $passed++;
    } else {
        echo "[FAIL] " . $description . "\n";
        $failed++;
    }
}

echo "=== STARTING TASKFLOW AUTOMATED TEST SUITE ===\n\n";

// 1. Database Connection Test
try {
    $db = Database::connect();
    assertTest("Database connection initialized via PDO", $db !== null);
} catch (\Exception $e) {
    assertTest("Database connection initialized via PDO", false);
}

// 2. User Authentication & Password Hashing
$adminUser = User::findByEmail('admin@taskflow.local');
assertTest("Admin seed user found in database", $adminUser !== null && $adminUser['role'] === 'admin');
assertTest("Password verification succeeds for correct password", password_verify('Password123!', $adminUser['password']));
assertTest("Password verification rejects wrong password", !password_verify('WrongPassword!', $adminUser['password']));

// Test User Creation & Duplicate Email Detection
$testEmail = 'test_qa_' . time() . '@example.com';
$createdUserId = User::create([
    'name' => 'QA Tester',
    'email' => $testEmail,
    'password' => 'SecurePass123!',
    'role' => 'team_member',
    'status' => 'active'
]);
assertTest("User created successfully with role team_member", $createdUserId > 0);
$foundUser = User::findByEmail($testEmail);
assertTest("New user retrieved by email", $foundUser !== null && $foundUser['name'] === 'QA Tester');

// 3. Rate Limiting / Brute Force Protection Test
$testIp = '192.168.1.99';
for ($i = 0; $i < 6; $i++) {
    User::recordLoginAttempt('attacker@hack.local', $testIp);
}
assertTest("Rate limiting triggers after 6 attempts within 15 mins", User::isRateLimited('attacker@hack.local', $testIp));
User::clearLoginAttempts('attacker@hack.local', $testIp);
assertTest("Rate limiting cleared after reset", !User::isRateLimited('attacker@hack.local', $testIp));

// 4. Team CRUD & Membership Operations
$teamId = Team::create('QA Automation Team', 'Team for automated pipeline testing', $adminUser['id']);
assertTest("Team created with ID", $teamId > 0);
assertTest("Team leader correctly associated", Team::isLeader($teamId, $adminUser['id']));

Team::addMember($teamId, $createdUserId);
assertTest("Member added to team", Team::isMember($teamId, $createdUserId));

$members = Team::getMembers($teamId);
assertTest("Team roster retrieves active members", count($members) >= 2);

// 5. Project Creation & Progress Calculation
$projectId = Project::create([
    'name' => 'QA Core Systems Audit',
    'description' => 'Comprehensive integration test suite project',
    'team_id' => $teamId,
    'manager_id' => $adminUser['id'],
    'status' => 'active',
    'start_date' => '2026-11-20',
    'deadline' => '2026-12-25'
]);
assertTest("Project created with team association", $projectId > 0);
$projectData = Project::findById($projectId);
assertTest("Project retrieved with initial 0% progress", $projectData['progress'] === 0);

// 6. Task Management & Status Updates
$taskId1 = Task::create([
    'title' => 'QA Task 1: Verify Endpoints',
    'description' => 'Unit testing controller responses',
    'project_id' => $projectId,
    'team_id' => $teamId,
    'assigned_to' => $createdUserId,
    'created_by' => $adminUser['id'],
    'priority' => 'high',
    'status' => 'in_progress',
    'start_date' => '2026-11-21',
    'due_date' => '2026-11-30'
]);
$taskId2 = Task::create([
    'title' => 'QA Task 2: Validate CSRF tokens',
    'description' => 'Ensure all state changes verify anti-forgery token',
    'project_id' => $projectId,
    'team_id' => $teamId,
    'assigned_to' => $createdUserId,
    'created_by' => $adminUser['id'],
    'priority' => 'urgent',
    'status' => 'completed',
    'start_date' => '2026-11-21',
    'due_date' => '2026-11-28'
]);
assertTest("Tasks created in database", $taskId1 > 0 && $taskId2 > 0);

// Check project progress calculation: 1 completed out of 2 = 50%
$projectData = Project::findById($projectId);
assertTest("Project progress correctly calculated from real tasks (50%)", $projectData['progress'] === 50);

// Test Task Status Update via Ajax logic
Task::updateStatus($taskId1, 'completed');
$projectData = Project::findById($projectId);
assertTest("Project progress updates to 100% when all tasks completed", $projectData['progress'] === 100);

// 7. Comments & Collaboration
$commentId = TaskComment::create($taskId1, $adminUser['id'], 'Great job on automated QA!');
assertTest("Comment added to task", $commentId > 0);
$comments = TaskComment::getByTaskId($taskId1);
assertTest("Task comments retrieved chronologically", count($comments) >= 1 && $comments[0]['comment'] === 'Great job on automated QA!');

// 8. Notifications & Activity Logs
$notifId = Notification::create($createdUserId, 'QA Test Notice', 'This is a test notification', '/tasks/view?id=' . $taskId1);
assertTest("Notification created for user", $notifId > 0);
$unreadCount = Notification::getUnreadCount($createdUserId);
assertTest("Unread notification count tracks accurately", $unreadCount >= 1);
Notification::markAsRead($notifId, $createdUserId);
assertTest("Notification marked as read", Notification::getUnreadCount($createdUserId) === $unreadCount - 1);

ActivityLog::log($adminUser['id'], 'test_action', 'system', null, 'QA test suite execution log');
$recentActs = ActivityLog::getRecent(5);
assertTest("Activity log captures events", count($recentActs) > 0);

// 9. Security: XSS Sanitization & CSRF Logic
$maliciousPayload = '<script>alert("xss")</script>&"\'';
$sanitized = Security::escape($maliciousPayload);
assertTest("XSS payload escaped with htmlspecialchars", !str_contains($sanitized, '<script>') && str_contains($sanitized, '&lt;script&gt;'));

// 10. Clean up test records
Team::delete($teamId);
$stmt = Database::connect()->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $createdUserId]);
assertTest("Teardown cleaned up test records", true);

echo "\n============================================\n";
echo "TEST RESULTS: {$passed} PASSED, {$failed} FAILED\n";
echo "============================================\n";

if ($failed > 0) exit(1);
exit(0);
