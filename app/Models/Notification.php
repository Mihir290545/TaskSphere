<?php
namespace App\Models;

use PDO;

class Notification extends Model {
    public static function create(int $userId, string $title, string $message, ?string $link = null): int {
        $stmt = self::getDb()->prepare(
            "INSERT INTO notifications (user_id, title, message, link, is_read) 
             VALUES (:user_id, :title, :message, :link, 0)"
        );
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ]);
        return (int)self::getDb()->lastInsertId();
    }

    public static function getForUser(int $userId, int $limit = 20): array {
        $stmt = self::getDb()->prepare(
            "SELECT * FROM notifications 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC LIMIT " . (int)$limit
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getUnreadCount(int $userId): int {
        $stmt = self::getDb()->prepare(
            "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0"
        );
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public static function markAsRead(int $id, int $userId): bool {
        $stmt = self::getDb()->prepare(
            "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public static function markAllAsRead(int $userId): bool {
        $stmt = self::getDb()->prepare(
            "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id"
        );
        return $stmt->execute(['user_id' => $userId]);
    }
}
