<?php
class PhotoModel extends BaseModel
{
    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM photos WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function create(int $userId, string $imagePath, string $caption): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO photos (user_id, image_path, caption) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $imagePath, $caption]);
    }

    public function findOwnedById(int $photoId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM photos WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$photoId, $userId]);

        return $stmt->fetch() ?: null;
    }

    public function updateCaption(int $photoId, int $userId, string $caption): void
    {
        $stmt = $this->db->prepare(
            'UPDATE photos SET caption = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$caption, $photoId, $userId]);
    }

    public function deleteById(int $photoId): void
    {
        $stmt = $this->db->prepare('DELETE FROM photos WHERE id = ?');
        $stmt->execute([$photoId]);
    }

    public function listImagePathsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT image_path FROM photos WHERE user_id = ?');
        $stmt->execute([$userId]);

        return array_map(
            static fn (array $row): string => (string) $row['image_path'],
            $stmt->fetchAll()
        );
    }
}
