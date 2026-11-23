<?php
namespace App\Models;

use PDO;

class Task extends Model {
    public static function findById(int $id): ?array {
        $stmt = self::getDb()->prepare(
            "SELECT t.*, p.name as project_name, tm.name as team_name, 
                    u_assign.name as assigned_name, u_assign.email as assigned_email, u_assign.avatar as assigned_avatar,
                    u_creator.name as creator_name 
             FROM tasks t 
             JOIN projects p ON t.project_id = p.id 
             JOIN teams tm ON t.team_id = tm.id 
             LEFT JOIN users u_assign ON t.assigned_to = u_assign.id 
             JOIN users u_creator ON t.created_by = u_creator.id 
             WHERE t.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();
        return $task ?: null;
    }

    public static function getAll(array $filters = [], ?int $forUserId = null, ?string $userRole = null): array {
        $sql = "SELECT t.*, p.name as project_name, tm.name as team_name, 
                       u_assign.name as assigned_name, u_assign.avatar as assigned_avatar,
                       u_creator.name as creator_name 
                FROM tasks t 
                JOIN projects p ON t.project_id = p.id 
                JOIN teams tm ON t.team_id = tm.id 
                LEFT JOIN users u_assign ON t.assigned_to = u_assign.id 
                JOIN users u_creator ON t.created_by = u_creator.id 
                WHERE 1=1";

        $params = [];

        // Role-based scoping
        if ($userRole === 'team_leader' && $forUserId !== null) {
            $sql .= " AND (tm.leader_id = :leader_id OR t.assigned_to = :assigned_leader OR tm.id IN (SELECT team_id FROM team_members WHERE user_id = :team_member_leader))";
            $params['leader_id'] = $forUserId;
            $params['assigned_leader'] = $forUserId;
            $params['team_member_leader'] = $forUserId;
        } elseif ($userRole === 'team_member' && $forUserId !== null) {
            $sql .= " AND (t.assigned_to = :my_user_id OR tm.id IN (SELECT team_id FROM team_members WHERE user_id = :my_team_user_id))";
            $params['my_user_id'] = $forUserId;
            $params['my_team_user_id'] = $forUserId;
        }

        // Filters
        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE :search OR t.description LIKE :search_desc)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_desc'] = "%{$filters['search']}%";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['project_id'])) {
            $sql .= " AND t.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        if (!empty($filters['team_id'])) {
            $sql .= " AND t.team_id = :team_id";
            $params['team_id'] = $filters['team_id'];
        }
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = :assigned_to";
            $params['assigned_to'] = $filters['assigned_to'];
        }
        if (!empty($filters['overdue']) && $filters['overdue'] === '1') {
            $sql .= " AND t.due_date < CURDATE() AND t.status != 'completed'";
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY t.created_at ASC";
                break;
            case 'due_date':
                $sql .= " ORDER BY (t.due_date IS NULL), t.due_date ASC";
                break;
            case 'priority':
                $sql .= " ORDER BY FIELD(t.priority, 'urgent', 'high', 'medium', 'low')";
                break;
            case 'status':
                $sql .= " ORDER BY FIELD(t.status, 'to_do', 'in_progress', 'review', 'blocked', 'completed')";
                break;
            default:
                $sql .= " ORDER BY t.created_at DESC";
                break;
        }

        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $stmt = self::getDb()->prepare(
            "INSERT INTO tasks (title, description, project_id, team_id, assigned_to, created_by, priority, status, start_date, due_date) 
             VALUES (:title, :description, :project_id, :team_id, :assigned_to, :created_by, :priority, :status, :start_date, :due_date)"
        );
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'project_id' => $data['project_id'],
            'team_id' => $data['team_id'],
            'assigned_to' => !empty($data['assigned_to']) ? $data['assigned_to'] : null,
            'created_by' => $data['created_by'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => $data['status'] ?? 'to_do',
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'due_date' => !empty($data['due_date']) ? $data['due_date'] : null,
        ]);
        return (int)self::getDb()->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $stmt = self::getDb()->prepare(
            "UPDATE tasks 
             SET title = :title, description = :description, project_id = :project_id, 
                 team_id = :team_id, assigned_to = :assigned_to, priority = :priority, 
                 status = :status, start_date = :start_date, due_date = :due_date 
             WHERE id = :id"
        );
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'project_id' => $data['project_id'],
            'team_id' => $data['team_id'],
            'assigned_to' => !empty($data['assigned_to']) ? $data['assigned_to'] : null,
            'priority' => $data['priority'],
            'status' => $data['status'],
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'due_date' => !empty($data['due_date']) ? $data['due_date'] : null,
            'id' => $id,
        ]);
    }

    public static function updateStatus(int $id, string $status): bool {
        $stmt = self::getDb()->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public static function delete(int $id): bool {
        $stmt = self::getDb()->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getDashboardMetrics(?int $userId = null, ?string $userRole = null): array {
        $db = self::getDb();
        $metrics = [
            'total_tasks' => 0,
            'pending_tasks' => 0,
            'in_progress_tasks' => 0,
            'completed_tasks' => 0,
            'blocked_tasks' => 0,
            'overdue_tasks' => 0,
            'total_projects' => 0,
            'active_projects' => 0,
            'total_members' => 0,
            'priority_distribution' => ['low' => 0, 'medium' => 0, 'high' => 0, 'urgent' => 0],
            'status_distribution' => ['to_do' => 0, 'in_progress' => 0, 'review' => 0, 'completed' => 0, 'blocked' => 0],
        ];

        // Filter scope
        $scope = "";
        $params = [];
        if ($userRole === 'team_leader' && $userId !== null) {
            $scope = " WHERE team_id IN (SELECT id FROM teams WHERE leader_id = :uid OR id IN (SELECT team_id FROM team_members WHERE user_id = :uid2))";
            $params['uid'] = $userId;
            $params['uid2'] = $userId;
        } elseif ($userRole === 'team_member' && $userId !== null) {
            $scope = " WHERE (assigned_to = :uid OR team_id IN (SELECT team_id FROM team_members WHERE user_id = :uid2))";
            $params['uid'] = $userId;
            $params['uid2'] = $userId;
        }

        // Task counts by status
        $statusQuery = "SELECT status, COUNT(*) as count FROM tasks {$scope} GROUP BY status";
        $stmt = $db->prepare($statusQuery);
        $stmt->execute($params);
        while ($row = $stmt->fetch()) {
            $st = $row['status'];
            $cnt = (int)$row['count'];
            $metrics['total_tasks'] += $cnt;
            if (isset($metrics['status_distribution'][$st])) {
                $metrics['status_distribution'][$st] = $cnt;
            }
            if ($st === 'to_do') $metrics['pending_tasks'] = $cnt;
            if ($st === 'in_progress') $metrics['in_progress_tasks'] = $cnt;
            if ($st === 'completed') $metrics['completed_tasks'] = $cnt;
            if ($st === 'blocked') $metrics['blocked_tasks'] = $cnt;
        }

        // Priority distribution
        $priorityQuery = "SELECT priority, COUNT(*) as count FROM tasks {$scope} GROUP BY priority";
        $stmt = $db->prepare($priorityQuery);
        $stmt->execute($params);
        while ($row = $stmt->fetch()) {
            if (isset($metrics['priority_distribution'][$row['priority']])) {
                $metrics['priority_distribution'][$row['priority']] = (int)$row['count'];
            }
        }

        // Overdue count
        $overdueQuery = "SELECT COUNT(*) FROM tasks " . ($scope ? "{$scope} AND" : "WHERE") . " due_date < CURDATE() AND status != 'completed'";
        $stmt = $db->prepare($overdueQuery);
        $stmt->execute($params);
        $metrics['overdue_tasks'] = (int)$stmt->fetchColumn();

        // Projects
        $projParams = [];
        $projScope = "";
        if ($userRole === 'team_leader' && $userId !== null) {
            $projScope = " WHERE team_id IN (SELECT id FROM teams WHERE leader_id = :uid OR id IN (SELECT team_id FROM team_members WHERE user_id = :uid2))";
            $projParams['uid'] = $userId;
            $projParams['uid2'] = $userId;
        } elseif ($userRole === 'team_member' && $userId !== null) {
            $projScope = " WHERE team_id IN (SELECT team_id FROM team_members WHERE user_id = :uid)";
            $projParams['uid'] = $userId;
        }
        $projStmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active FROM projects {$projScope}");
        $projStmt->execute($projParams);
        $projRow = $projStmt->fetch();
        $metrics['total_projects'] = (int)($projRow['total'] ?? 0);
        $metrics['active_projects'] = (int)($projRow['active'] ?? 0);

        // Members
        $metrics['total_members'] = (int)$db->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();

        return $metrics;
    }
}
