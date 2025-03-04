<?php
require_once __DIR__ . '/../config/db.php';

class SearchController {
    public function search() {
        $activity = trim($_GET['activity'] ?? '');
        $city = trim($_GET['city'] ?? '');

        if (empty($activity) || empty($city)) {
            header('Location: /error');
            exit;
        }

        try {
            $pdo = (new DbConfig())->getConnection();
            $sql = "SELECT ... WHERE profession LIKE :activity AND city LIKE :city";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['activity' => "%$activity%", 'city' => "%$city%"]);

            $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Passer les données à la vue
            require_once __DIR__ . '/findservice/';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('Location: /error');
            exit;
        }
    }
}