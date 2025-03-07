<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $workerId = filter_input(INPUT_POST, 'worker_id', FILTER_SANITIZE_NUMBER_INT);
    $appointmentDate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);

    if ($workerId && $appointmentDate) {
        try {
            $dbConfig = new DbConfig();
            $pdo = $dbConfig->getConnection();

            // Vérifiez si le worker existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM workers WHERE user_id = :worker_id");
            $stmt->execute([':worker_id' => $workerId]);
            $workerExists = $stmt->fetchColumn();

            if ($workerExists) {
                // Insérer le rendez-vous
                $sql = "INSERT INTO services (worker_id, appointment_date) VALUES (:worker_id, :appointment_date)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':worker_id', $workerId);
                $stmt->bindValue(':appointment_date', $appointmentDate);
                $stmt->execute();

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Worker ID does not exist']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => htmlspecialchars($e->getMessage())]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}