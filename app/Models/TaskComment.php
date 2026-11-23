<?php
namespace App\Models;

use PDO;

class TaskComment extends Model {
    public static function getByTaskId(int $taskId): array {
        $stmt = self::getDb()->prepare(
            "SELECT tc.*, u.name as user_name, u.role as user_role, u.avatar as user_avatar 
             FROM task_comments tc 
             JOIN users u ON tc.user_id = u.id 
             WHERE tc.task_id = :task_id 
             ORDER BY tc.created_at ASC"
        );
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    public static function create(int $taskId, int $userId, string $comment): int {
        $stmt = self::getDb()->prepare(
            "INSERT INTO task_comments (task_id, user_id, comment) VALUES (:task_id, :user_id, :comment)"
        );
        $stmt->execute([
            'task_id' => $taskId,
            'user_id' => $userId,
            'comment' => $comment
        ]);
        return (int)self::getDb()->lastInsertId();
    }

    public static function findById(int $id): ?array {
        $stmt = self::getDb()->prepare("SELECT * FROM task_comments WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $c = $stmt->fetch();
        return $c ?: null;
    }

    public static function delete(int $id): bool {
        $stmt = self::getDb()->prepare("DELETE FROM task_comments WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
