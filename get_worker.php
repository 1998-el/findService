<?php
require_once __DIR__ . '/src/app/config/db.php';
header('Content-Type: application/json');

try {
    $db = new DbConfig();
    $pdo = $db->getConnection();

    $workerId = filter_input(INPUT_GET, 'worker_id', FILTER_VALIDATE_INT);
    
    if(!$workerId || $workerId < 1) {
        throw new Exception('ID professionnel invalide', 400);
    }

    $stmt = $pdo->prepare("
        SELECT 
            w.id, 
            w.profession, 
            w.hourly_rate, 
            w.description, 
            w.photo_url,
            u.first_name, 
            u.last_name, 
            u.city
        FROM workers w
        INNER JOIN users u ON w.user_id = u.id
        WHERE w.id = :id
    ");
    
    $stmt->bindParam(':id', $workerId, PDO::PARAM_INT);
    $stmt->execute();
    
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$worker) {
        throw new Exception('Professionnel non trouvé', 404);
    }

    // Filtrage des données sensibles
    $allowedFields = [
        'id', 'profession', 'hourly_rate', 'description',
        'photo_url', 'first_name', 'last_name', 'city'
    ];
    $filteredData = array_intersect_key($worker, array_flip($allowedFields));

    echo json_encode($filteredData);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}