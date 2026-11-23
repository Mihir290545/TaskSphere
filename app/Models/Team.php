<?php
namespace App\Models;

use PDO;

class Team extends Model {
    public static function findById(int $id): ?array {
        $stmt = self::getDb()->prepare(
            "SELECT t.*, u.name as leader_name, u.email as leader_email 
             FROM teams t 
             LEFT JOIN users u ON t.leader_id = u.id 
             WHERE t.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $team = $stmt->fetch();
        return $team ?: null;
    }

    public static function getAll(?int $forUserId = null, ?string $userRole = null): array {
        $sql = "SELECT t.*, u.name as leader_name, 
                       (SELECT COUNT(*) FROM team_members WHERE team_id = t.id) as member_count,
                       (SELECT COUNT(*) FROM projects WHERE team_id = t.id) as project_count,
                       (SELECT COUNT(*) FROM tasks WHERE team_id = t.id) as task_count
                FROM teams t 
                LEFT JOIN users u ON t.leader_id = u.id";

        $params = [];
        if ($userRole === 'team_leader' && $forUserId !== null) {
            $sql .= " WHERE t.leader_id = :leader_id OR t.id IN (SELECT team_id FROM team_members WHERE user_id = :user_id)";
            $params['leader_id'] = $forUserId;
            $params['user_id'] = $forUserId;
        } elseif ($userRole === 'team_member' && $forUserId !== null) {
            $sql .= " WHERE t.id IN (SELECT team_id FROM team_members WHERE user_id = :user_id)";
            $params['user_id'] = $forUserId;
        }

        $sql .= " ORDER BY t.name ASC";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(string $name, ?string $description, ?int $leaderId): int {
        $stmt = self::getDb()->prepare(
            "INSERT INTO teams (name, description, leader_id) VALUES (:name, :description, :leader_id)"
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'leader_id' => $leaderId
        ]);
        $teamId = (int)self::getDb()->lastInsertId();

        if ($leaderId) {
            self::addMember($teamId, $leaderId);
        }
        return $teamId;
    }

    public static function update(int $id, string $name, ?string $description, ?int $leaderId): bool {
        $stmt = self::getDb()->prepare(
            "UPDATE teams SET name = :name, description = :description, leader_id = :leader_id WHERE id = :id"
        );
        $result = $stmt->execute([
            'name' => $name,
            'description' => $description,
            'leader_id' => $leaderId,
            'id' => $id
        ]);
        if ($leaderId) {
            self::addMember($id, $leaderId);
        }
        return $result;
    }

    public static function delete(int $id): bool {
        $stmt = self::getDb()->prepare("DELETE FROM teams WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getMembers(int $teamId): array {
        $stmt = self::getDb()->prepare(
            "SELECT u.id, u.name, u.email, u.role, u.avatar, tm.created_at as joined_at 
             FROM team_members tm 
             JOIN users u ON tm.user_id = u.id 
             WHERE tm.team_id = :team_id 
             ORDER BY u.name ASC"
        );
        $stmt->execute(['team_id' => $teamId]);
        return $stmt->fetchAll();
    }

    public static function addMember(int $teamId, int $userId): bool {
        $stmt = self::getDb()->prepare(
            "INSERT IGNORE INTO team_members (team_id, user_id) VALUES (:team_id, :user_id)"
        );
        return $stmt->execute(['team_id' => $teamId, 'user_id' => $userId]);
    }

    public static function removeMember(int $teamId, int $userId): bool {
        $stmt = self::getDb()->prepare(
            "DELETE FROM team_members WHERE team_id = :team_id AND user_id = :user_id"
        );
        return $stmt->execute(['team_id' => $teamId, 'user_id' => $userId]);
    }

    public static function isMember(int $teamId, int $userId): bool {
        $stmt = self::getDb()->prepare(
            "SELECT COUNT(*) FROM team_members WHERE team_id = :team_id AND user_id = :user_id"
        );
        $stmt->execute(['team_id' => $teamId, 'user_id' => $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function isLeader(int $teamId, int $userId): bool {
        $stmt = self::getDb()->prepare(
            "SELECT COUNT(*) FROM teams WHERE id = :team_id AND leader_id = :leader_id"
        );
        $stmt->execute(['team_id' => $teamId, 'leader_id' => $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
