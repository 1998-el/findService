<?php
// Définir la page courante
$currentPage = 'home';

// Inclure la connexion à la base de données
require_once __DIR__ . '/src/app/config/DbConfig.php'; // Chemin absolu vers DbConfig.php

// Chemin du dossier où les images seront stockées
$uploadDir = __DIR__ . '/src/app/view/uploads/'; // Chemin corrigé vers src/app/view/uploads

// Vérifier si le dossier existe, sinon le créer
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Traitement du formulaire de téléversement d'image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $fileName = basename($_FILES['photo']['name']);
    $filePath = $uploadDir . $fileName;

    // Déplacer le fichier téléversé vers le dossier des uploads
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
        // Enregistrer le chemin de l'image dans la base de données
        $stmt = $pdo->prepare("UPDATE workers SET photo_url = ? WHERE id = ?");
        $stmt->execute([$fileName, $workerId]);

        echo "Image téléversée avec succès.";
    } else {
        echo "Erreur lors du téléversement de l'image.";
    }
}

// Récupérer les workers avec les informations des utilisateurs
$sql = "
    SELECT 
        users.id AS user_id,
        users.first_name,
        users.last_name,
        users.email,
        users.phone,
        users.address,
        users.city,
        workers.id AS worker_id,
        workers.profession,
        workers.description,
        workers.hourly_rate,
        workers.availability,
        workers.photo_url
    FROM 
        workers
    INNER JOIN 
        users 
    ON 
        workers.user_id = users.id;
";

try {
    $stmt = $pdo->query($sql);
    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des workers : " . $e->getMessage());
}

// Inclure le header
require_once __DIR__ . '/src/app/component/Header.php'; // Chemin absolu vers Header.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Service</title>
    <link rel="stylesheet" href="src/app/style/main.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <header class="hero-section">
        <div class="hero-content">
            <h1>Welcome to Our Service</h1>
            <p>Explore our features and services that can help you achieve your goals.</p>
            <a href="<?php echo getRoute('explore'); ?>" class="btn">Explore Now</a>
        </div>
    </header>

    <section class="services-section">
        <h1 class="section-title">Our Team</h1>
        <div class="services-container">
            <?php foreach ($workers as $worker): ?>
                <div class="service-card">
                    <!-- Afficher l'image à partir du chemin -->
                    <?php if (!empty($worker['photo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($uploadDir . $worker['photo_url']); ?>" alt="<?php echo htmlspecialchars($worker['first_name'] . ' ' . $worker['last_name']); ?>" class="service-image">
                    <?php else: ?>
                        <img src="chemin/vers/image_par_defaut.jpg" alt="Image par défaut" class="service-image">
                    <?php endif; ?>
                    <h2 class="service-title"><?php echo htmlspecialchars($worker['first_name'] . ' ' . $worker['last_name']); ?></h2>
                    <h3 class="service-job-title"><?php echo htmlspecialchars($worker['profession']); ?></h3>
                    <p class="service-description">
                        <?php echo htmlspecialchars($worker['description']); ?>
                    </p>
                    <p class="service-rate">Tarif horaire : <?php echo htmlspecialchars($worker['hourly_rate']); ?> €</p>
                    <p class="service-availability">Disponibilité : <?php echo htmlspecialchars($worker['availability']); ?></p>
                    <a href="<?php echo getRoute('explore'); ?>" class="btn">Learn More</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="team-section">
        <div class="team-container">
            <img src="src/app/assets/images/home2-removebg-preview.png" alt="Our Team" class="team-image">
            <div class="team-content">
                <h2 class="team-title">Our Team</h2>
                <p class="team-description">
                    Meet our team of experts who are passionate about delivering high-quality services.
                </p>
            </div>
        </div>
    </section>

    <footer class="footer_b">
        <div class="footer-container">
            <!-- Section À Propos -->
            <div class="footer-section">
                <h3 class="footer-title">About Us</h3>
                <p class="footer-description">
                    We are dedicated to providing high-quality services and solutions to help you achieve your goals.
                </p>
            </div>

            <!-- Liens Utiles -->
            <div class="footer-section">
                <h3 class="footer-title">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo getRoute('home'); ?>">Home</a></li>
                    <li><a href="<?php echo getRoute('explore'); ?>">Services</a></li>
                    <li><a href="<?php echo getRoute('about'); ?>">About Us</a></li>
                    <li><a href="<?php echo getRoute('contact'); ?>">Contact</a></li>
                    <li><a href="<?php echo getRoute('login'); ?>">Login</a></li>
                </ul>
            </div>

            <!-- Informations de Contact -->
            <div class="footer-section">
                <h3 class="footer-title">Contact Us</h3>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Main Street, City, Country</li>
                    <li><i class="fas fa-phone"></i> +123 456 7890</li>
                    <li><i class="fas fa-envelope"></i> info@example.com</li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="footer-section">
                <h3 class="footer-title">Newsletter</h3>
                <p class="footer-description">
                    Subscribe to our newsletter to get the latest updates and offers.
                </p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Réseaux Sociaux -->
        <div class="footer-social">
            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>&copy; 2025 Your Company. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>