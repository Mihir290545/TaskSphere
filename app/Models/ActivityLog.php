<?php
namespace App\Models;

use PDO;

class ActivityLog extends Model {
    public static function log(?int $userId, string $action, string $entityType, ?int $entityId, string $description): void {
        $stmt = self::getDb()->prepare(
            "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description) 
             VALUES (:user_id, :action, :entity_type, :entity_id, :description)"
        );
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description
        ]);
    }

    public static function getRecent(int $limit = 10, ?int $userId = null): array {
        $sql = "SELECT a.*, u.name as user_name, u.avatar as user_avatar, u.role as user_role 
                FROM activity_logs a 
                LEFT JOIN users u ON a.user_id = u.id";
        
        $params = [];
        if ($userId !== null) {
            $sql .= " WHERE a.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT " . (int)$limit;
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
