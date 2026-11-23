<?php
namespace App\Models;

use PDO;

class Project extends Model {
    public static function findById(int $id): ?array {
        $stmt = self::getDb()->prepare(
            "SELECT p.*, t.name as team_name, u.name as manager_name,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed') as completed_tasks
             FROM projects p 
             JOIN teams t ON p.team_id = t.id 
             LEFT JOIN users u ON p.manager_id = u.id 
             WHERE p.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $project = $stmt->fetch();
        if ($project) {
            $total = (int)$project['total_tasks'];
            $completed = (int)$project['completed_tasks'];
            $project['progress'] = $total > 0 ? (int)round(($completed / $total) * 100) : 0;
        }
        return $project ?: null;
    }

    public static function getAll(?int $forUserId = null, ?string $userRole = null): array {
        $sql = "SELECT p.*, t.name as team_name, u.name as manager_name,
                       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed') as completed_tasks
                FROM projects p 
                JOIN teams t ON p.team_id = t.id 
                LEFT JOIN users u ON p.manager_id = u.id";

        $params = [];
        if ($userRole === 'team_leader' && $forUserId !== null) {
            $sql .= " WHERE t.leader_id = :leader_id OR p.manager_id = :manager_id OR t.id IN (SELECT team_id FROM team_members WHERE user_id = :user_id)";
            $params['leader_id'] = $forUserId;
            $params['manager_id'] = $forUserId;
            $params['user_id'] = $forUserId;
        } elseif ($userRole === 'team_member' && $forUserId !== null) {
            $sql .= " WHERE t.id IN (SELECT team_id FROM team_members WHERE user_id = :user_id)";
            $params['user_id'] = $forUserId;
        }

        $sql .= " ORDER BY p.deadline ASC, p.created_at DESC";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        $projects = $stmt->fetchAll();

        foreach ($projects as &$p) {
            $total = (int)$p['total_tasks'];
            $completed = (int)$p['completed_tasks'];
            $p['progress'] = $total > 0 ? (int)round(($completed / $total) * 100) : 0;
        }
        return $projects;
    }

    public static function create(array $data): int {
        $stmt = self::getDb()->prepare(
            "INSERT INTO projects (name, description, team_id, manager_id, status, start_date, deadline) 
             VALUES (:name, :description, :team_id, :manager_id, :status, :start_date, :deadline)"
        );
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'team_id' => $data['team_id'],
            'manager_id' => $data['manager_id'] ?? null,
            'status' => $data['status'] ?? 'planning',
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'deadline' => !empty($data['deadline']) ? $data['deadline'] : null,
        ]);
        return (int)self::getDb()->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $stmt = self::getDb()->prepare(
            "UPDATE projects 
             SET name = :name, description = :description, team_id = :team_id, 
                 manager_id = :manager_id, status = :status, start_date = :start_date, deadline = :deadline 
             WHERE id = :id"
        );
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'team_id' => $data['team_id'],
            'manager_id' => $data['manager_id'] ?? null,
            'status' => $data['status'],
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'deadline' => !empty($data['deadline']) ? $data['deadline'] : null,
            'id' => $id,
        ]);
    }

    public static function delete(int $id): bool {
        $stmt = self::getDb()->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
