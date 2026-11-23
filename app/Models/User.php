<?php
namespace App\Models;

use PDO;

class User extends Model {
    public static function findById(int $id): ?array {
        $stmt = self::getDb()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $stmt = self::getDb()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int {
        $sql = "INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? 'team_member',
            'status' => $data['status'] ?? 'active',
        ]);
        return (int)self::getDb()->lastInsertId();
    }

    public static function updateProfile(int $id, string $name, ?string $avatar = null): bool {
        $sql = "UPDATE users SET name = :name" . ($avatar !== null ? ", avatar = :avatar" : "") . " WHERE id = :id";
        $params = ['name' => $name, 'id' => $id];
        if ($avatar !== null) $params['avatar'] = $avatar;
        return self::getDb()->prepare($sql)->execute($params);
    }

    public static function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = self::getDb()->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute(['password' => $hash, 'id' => $id]);
    }

    public static function updateStatus(int $id, string $status): bool {
        $stmt = self::getDb()->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public static function updateRole(int $id, string $role): bool {
        $stmt = self::getDb()->prepare("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public static function getAll(string $search = '', string $role = '', string $status = ''): array {
        $sql = "SELECT u.id, u.name, u.email, u.role, u.status, u.avatar, u.created_at,
                       (SELECT COUNT(*) FROM tasks WHERE assigned_to = u.id) as task_count,
                       (SELECT GROUP_CONCAT(t.name SEPARATOR ', ') FROM teams t JOIN team_members tm ON t.id = tm.team_id WHERE tm.user_id = u.id) as team_names
                FROM users u WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (u.name LIKE :search OR u.email LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        if ($role !== '') {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }
        if ($status !== '') {
            $sql .= " AND u.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY u.created_at DESC";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getTeamLeaders(): array {
        $stmt = self::getDb()->query("SELECT id, name, email FROM users WHERE role IN ('team_leader', 'admin') AND status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function getActiveMembers(): array {
        $stmt = self::getDb()->query("SELECT id, name, email, role FROM users WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    // Rate Limiting Login Attempts
    public static function recordLoginAttempt(string $email, string $ip): void {
        $stmt = self::getDb()->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
    }

    public static function isRateLimited(string $email, string $ip): bool {
        $stmt = self::getDb()->prepare(
            "SELECT COUNT(*) FROM login_attempts 
             WHERE (email = :email OR ip_address = :ip) 
             AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
        );
        $stmt->execute(['email' => $email, 'ip' => $ip]);
        return (int)$stmt->fetchColumn() >= 6; // Max 6 attempts per 15 min
    }

    public static function clearLoginAttempts(string $email, string $ip): void {
        $stmt = self::getDb()->prepare("DELETE FROM login_attempts WHERE email = :email OR ip_address = :ip");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
    }
}
