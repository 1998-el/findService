<?php
// dashboard.php

session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Vérifier si l'utilisateur est connecté et est un Worker
if (!isWorkerLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les informations du Worker
$workerId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM workers WHERE id = ?");
$stmt->execute([$workerId]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les services du Worker
$servicesStmt = $pdo->prepare("SELECT * FROM services WHERE worker_id = ?");
$servicesStmt->execute([$workerId]);
$services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Worker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Accueil</a></li>
                <li><a href="profile.php">Modifier le Profil</a></li>
                <li><a href="chat.php">Messagerie</a></li>
                <li><a href="../includes/logout.php">Déconnexion</a></li>
            </ul>
        </div>

        <!-- Contenu principal -->
        <div class="main-content">
            <h1>Bienvenue, <?php echo htmlspecialchars($worker['first_name']); ?> !</h1>

            <!-- Informations du Worker -->
            <div class="profile-info">
                <h2>Vos Informations</h2>
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($worker['last_name']); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($worker['first_name']); ?></p>
                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($worker['phone']); ?></p>
                <p><strong>Adresse :</strong> <?php echo htmlspecialchars($worker['address']); ?></p>
                <p><strong>Tarif Horaire :</strong> <?php echo htmlspecialchars($worker['hourly_rate']); ?> €/h</p>
            </div>

            <!-- Services proposés -->
            <div class="services">
                <h2>Vos Services</h2>
                <?php if (empty($services)): ?>
                    <p>Aucun service enregistré.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($services as $service): ?>
                            <li><?php echo htmlspecialchars($service['name']); ?> - <?php echo htmlspecialchars($service['description']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>