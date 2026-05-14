<?php
class SearchController {

    public function index(): void {
        $query   = trim($_GET['q'] ?? '');
        $results = [];

        if ($query !== '') {
            $db   = getDB();
            $stmt = $db->prepare("
                SELECT u.id, u.username, u.display_name, u.profile_image,
                       COUNT(p.id) AS total_fotos
                FROM users u
                LEFT JOIN photos p ON p.user_id = u.id
                WHERE u.username LIKE ?
                   OR u.display_name LIKE ?
                GROUP BY u.id
                ORDER BY u.username ASC
                LIMIT 20
            ");
            $like = '%' . $query . '%';
            $stmt->execute([$like, $like]);
            $results = $stmt->fetchAll();
        }

        require ROOT_PATH . '/app/views/search/index.php';
    }
}