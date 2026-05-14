<?php
class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        if ($excludeUserId === null) {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
        } else {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');
            $stmt->execute([$username, $excludeUserId]);
        }

        return (bool) $stmt->fetchColumn();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $username, string $displayName, string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, display_name, email, password_hash) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $displayName, $email, $passwordHash]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $username, string $displayName, string $bio): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET username = ?, display_name = ?, bio = ? WHERE id = ?'
        );
        $stmt->execute([$username, $displayName, $bio, $id]);
    }

    public function searchWithPhotoCounts(string $query): array
    {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            'SELECT u.id, u.username, u.display_name, u.profile_image,
                    COUNT(p.id) AS total_fotos
             FROM users u
             LEFT JOIN photos p ON p.user_id = u.id
             WHERE u.username LIKE ?
                OR u.display_name LIKE ?
             GROUP BY u.id
             ORDER BY u.username ASC
             LIMIT 20'
        );
        $stmt->execute([$like, $like]);

        return $stmt->fetchAll();
    }

    public function getProfileImage(int $id): ?string
    {
        $stmt = $this->db->prepare('SELECT profile_image FROM users WHERE id = ?');
        $stmt->execute([$id]);

        $image = $stmt->fetchColumn();

        return $image !== false ? (string) $image : null;
    }

    public function updateProfileImage(int $id, ?string $filename): void
    {
        $stmt = $this->db->prepare('UPDATE users SET profile_image = ? WHERE id = ?');
        $stmt->execute([$filename, $id]);
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
}
