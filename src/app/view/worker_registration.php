<?php
// Configuration
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/image_processor.php';

session_start();

// Vérification de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: Register.php');
    exit;
}

// Récupération de l'ID utilisateur
$userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

if (!$userId) {
    die('ID utilisateur invalide.');
}

// Connexion à la base de données
try {
    $dbConfig = new DbConfig();
    $pdo = $dbConfig->getConnection();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}

// Configuration des uploads
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $services = filter_input(INPUT_POST, 'services', FILTER_SANITIZE_STRING);
    $hourlyRate = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $profilePhoto = $_FILES['profile_photo'] ?? null;

    if (empty($services) || !$hourlyRate || empty($description)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        try {
            $pdo->beginTransaction();

            // Traitement de l'image
            $imageUrl = null;
            if ($profilePhoto && $profilePhoto['error'] === UPLOAD_ERR_OK) {
                $imageProcessor = new ImageProcessor();
                $imageUrl = $imageProcessor->processUpload($profilePhoto, $uploadDir, $userId);
            }

            // Mise à jour des informations du worker
            $stmt = $pdo->prepare("
                INSERT INTO workers 
                (user_id, profession, description, hourly_rate, availability, photo_url) 
                VALUES (?, ?, ?, ?, 'available', ?)
                ON DUPLICATE KEY UPDATE 
                profession = VALUES(profession),
                description = VALUES(description),
                hourly_rate = VALUES(hourly_rate),
                photo_url = VALUES(photo_url)
            ");
            $stmt->execute([$userId, $services, $description, $hourlyRate, $imageUrl]);

            $pdo->commit();
            header('Location: dashboard.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Erreur : ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compléter l'inscription - Worker</title>
    <link rel="stylesheet" href="../style/worker.css">
</head>
<body>
    <div class="register-container">
        <div class="form-section">
            <h2>Compléter l'inscription - Worker</h2>
            
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="profile_photo">Photo de profil</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewProfilePhoto(event)">
                </div>

                <div class="input-group">
                    <label for="services">Services proposés</label>
                    <select id="services" name="services" required>
                        <option value="">-- Sélectionnez un service --</option>
                        <?php
                        $servicesList = [
                            'nettoyage' => 'Nettoyage',
                            'plomberie' => 'Plomberie',
                            'electricite' => 'Électricité',
                            'jardinage' => 'Jardinage',
                            'peinture' => 'Peinture',
                            'demenagement' => 'Déménagement'
                        ];
                        
                        foreach ($servicesList as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($services ?? '') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label for="hourly_rate">Facturation horaire (€)</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" min="10" max="500" step="1" 
                           value="<?= htmlspecialchars($hourlyRate ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn">Soumettre</button>
            </form>
        </div>

        <div class="preview-section">
            <img id="profile-preview" src="" alt="Aperçu de la photo de profil">
            <p>Aperçu de la photo de profil</p>
        </div>
    </div>

    <script>
        function previewProfilePhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('profile-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>