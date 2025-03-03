<?php
// Fonction pour redimensionner une image
function resizeImage($file, $maxWidth, $maxHeight) {
    // Vérifier si le fichier est une image valide
    if (!file_exists($file)) {
        throw new Exception("Le fichier image n'existe pas.");
    }

    // Obtenir les informations sur l'image
    list($width, $height, $type) = getimagesize($file);

    // Calculer les nouvelles dimensions
    $ratio = $width / $height;
    if ($maxWidth / $maxHeight > $ratio) {
        $newWidth = $maxHeight * $ratio;
        $newHeight = $maxHeight;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    }

    // Créer une nouvelle image redimensionnée
    $src = imagecreatefromstring(file_get_contents($file));
    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Conserver la transparence pour les images PNG et GIF
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    // Redimensionner l'image
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Enregistrer l'image redimensionnée dans un fichier temporaire
    $tempFile = tempnam(sys_get_temp_dir(), 'img');
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $tempFile, 90); // Qualité de 90%
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $tempFile, 9); // Compression de niveau 9
            break;
        case IMAGETYPE_GIF:
            imagegif($dst, $tempFile);
            break;
        default:
            throw new Exception("Type d'image non supporté.");
    }

    // Libérer la mémoire
    imagedestroy($src);
    imagedestroy($dst);

    return $tempFile;
}

// Début du script existant
session_start();

// Vérifier si l'utilisateur est connecté et a le rôle Worker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: Register.php');
    exit;
}

// Récupérer l'ID de l'utilisateur depuis l'URL
$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    die('ID utilisateur manquant.');
}

// Connexion à la base de données
$host = '127.0.0.1';
$db = 'services';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$uploadDir = __DIR__ . '/uploads';

// Vérifier si le dossier existe, sinon le créer
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Traitement du formulaire supplémentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture des données du formulaire
    $profilePhoto = $_FILES['profile_photo']['tmp_name'] ?? '';
    $services = $_POST['services'] ?? '';
    $hourlyRate = $_POST['hourly_rate'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validation des données
    if (empty($services) || empty($hourlyRate) || empty($description)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        try {
            // Début de la transaction
            $pdo->beginTransaction();

            // Téléverser et redimensionner l'image
            if (!empty($profilePhoto)) {
                // Récupérer le nom et l'extension du fichier
                $originalFileName = $_FILES['profile_photo']['name'];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION); // Extraire l'extension

                // Générer un nom de fichier unique avec l'extension d'origine
                $fileName = 'worker_' . $userId . '_' . uniqid() . '.' . $fileExtension;
                $filePath = $uploadDir . '/' . $fileName;

                // Redimensionner l'image
                $resizedPhoto = resizeImage($profilePhoto, 800, 800); // Redimensionner à 800x800 pixels

                // Déplacer l'image redimensionnée vers le dossier des uploads
                if (rename($resizedPhoto, $filePath)) {
                    echo "Fichier téléversé avec succès : " . $filePath . "<br>";

                    // Générer l'URL de l'image
                    $imageUrl = $fileName; // Chemin relatif pour l'URL
                    echo "URL de l'image : " . $imageUrl . "<br>";

                    // Enregistrer l'URL de l'image dans la base de données
                    $stmt = $pdo->prepare("UPDATE workers SET photo_url = ? WHERE user_id = ?");
                    if ($stmt->execute([$imageUrl, $userId])) {
                        echo "URL de l'image mise à jour dans la base de données.<br>";
                    } else {
                        throw new Exception("Erreur lors de la mise à jour de l'URL de l'image.");
                    }
                } else {
                    throw new Exception("Erreur lors du déplacement de l'image.");
                }
            }

            // Insérer les informations supplémentaires dans la table `workers`
            $stmt = $pdo->prepare("INSERT INTO workers (user_id, profession, description, hourly_rate, availability, photo_url) VALUES (?, ?, ?, ?, 'available', ?)");
            $stmt->execute([$userId, $services, $description, $hourlyRate, $imageUrl]);

            // Valider la transaction
            $pdo->commit();

            // Rediriger vers la page d'accueil
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Erreur lors de la mise à jour des informations : ' . $e->getMessage();
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $e->getMessage();
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
        <!-- Section du formulaire -->
        <div class="form-section">
            <h2>Compléter l'inscription - Worker</h2>
            <!-- Affichage des erreurs -->
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="worker_registration.php?user_id=<?php echo $userId; ?>" method="POST" enctype="multipart/form-data">
                <!-- Photo de profil -->
                <div class="input-group">
                    <label for="profile_photo">Photo de profil</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" aria-label="Téléchargez votre photo de profil" onchange="previewProfilePhoto(event)">
                </div>

                <!-- Liste de services -->
                <div class="input-group">
                    <label for="services">Services proposés</label>
                    <select id="services" name="services" required>
                        <option value="" disabled selected>-- Sélectionnez un service --</option>
                        <option value="nettoyage">Nettoyage</option>
                        <option value="plomberie">Plomberie</option>
                        <option value="electricite">Électricité</option>
                        <option value="jardinage">Jardinage</option>
                        <option value="peinture">Peinture</option>
                        <option value="demenagement">Déménagement</option>
                    </select>
                </div>

                <!-- Facturation horaire -->
                <div class="input-group">
                    <label for="hourly_rate">Facturation horaire (€)</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" min="10" max="500" step="1" required aria-label="Entrez votre tarif horaire">
                </div>

                <!-- Description -->
                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required aria-label="Entrez votre description"></textarea>
                </div>

                <!-- Bouton de soumission -->
                <button type="submit" class="btn">Soumettre</button>
            </form>
        </div>

        <!-- Section de prévisualisation de la photo -->
        <div class="preview-section">
            <img id="profile-preview" src="" alt="Aperçu de la photo de profil">
            <p>Aperçu de la photo de profil</p>
        </div>
    </div>

    <!-- Script pour la prévisualisation de la photo -->
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