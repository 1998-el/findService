<?php
require_once __DIR__ . '../config/db.php';
header('Content-Type: application/json');

session_start();

try {
    // Vérification authentification
    if(empty($_SESSION['user_id'])) {
        throw new Exception('Authentification requise', 401);
    }

    $db = new DbConfig();
    $pdo = $db->getConnection();

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation des données
    if(empty($data['worker_id']) || empty($data['date'])) {
        throw new Exception('Données manquantes', 400);
    }

    $workerId = filter_var($data['worker_id'], FILTER_VALIDATE_INT);
    $date = DateTime::createFromFormat('Y-m-d', $data['date']);
    
    if(!$workerId || !$date) {
        throw new Exception('Données invalides', 400);
    }

    // Vérification disponibilité
    $stmt = $pdo->prepare("
        SELECT * FROM appointments 
        WHERE worker_id = ? AND DATE(appointment_date) = ?
    ");
    $stmt->execute([$workerId, $date->format('Y-m-d')]);
    
    if($stmt->rowCount() > 0) {
        throw new Exception('Créneau déjà réservé', 409);
    }

    // Création du rendez-vous
    $stmt = $pdo->prepare("
        INSERT INTO appointments 
        (worker_id, user_id, appointment_date, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    
    $success = $stmt->execute([
        $workerId,
        $_SESSION['user_id'],
        $date->format('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Rendez-vous confirmé' : 'Échec de la réservation',
        'appointment_id' => $success ? $pdo->lastInsertId() : null
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}