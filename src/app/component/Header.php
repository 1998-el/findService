<?php
// Démarrez la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier des fonctions
require_once __DIR__ . '/../functions/routes.php';

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Vérifie si l'ID de l'utilisateur est stocké dans la session
}

// Logique de déconnexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Supprimer le token de l'utilisateur (si vous utilisez des tokens)
    if (isset($_SESSION['user_token'])) {
        unset($_SESSION['user_token']);
    }

    // Détruire la session
    session_unset(); // Vide toutes les variables de session
    session_destroy(); // Détruit la session

    // Répondre avec un statut JSON pour indiquer que la déconnexion a réussi
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    exit();
}

// Définir la page actuelle (exemple : 'explore', 'about', 'contact')
$currentPage = 'explore'; // Remplacez cette valeur par la page actuelle dynamiquement
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/app/style/header.css">
    <title>Header</title>
</head>
<body>
<div id="loading-spinner" class="loading-spinner" style="display: none;">
    <div class="spinner"></div>
</div>
    <nav class="navbar" aria-label="Main Navigation">
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo">H<span>Service</span></h1>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo getRoute('explore'); ?>" class="nav-link <?php echo ($currentPage == 'explore') ? 'active' : ''; ?>">Explore</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo getRoute('about'); ?>" class="nav-link <?php echo ($currentPage == 'about') ? 'active' : ''; ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo getRoute('contact'); ?>" class="nav-link <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">Contact</a>
                </li>
            </ul>
            <div class="login_container">
                <?php if (isLoggedIn()): ?>
                    <!-- Afficher un bouton de déconnexion si l'utilisateur est connecté -->
                    <button id="logout-button" class="logout-button">Sign Out</button>
                <?php else: ?>
                    <!-- Afficher "Sign In" si l'utilisateur n'est pas connecté -->
                    <a href="<?php echo getRoute('login'); ?>">Sign In</a>
                <?php endif; ?>
            </div>
            <button class="hamburger" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav>
    <script src="src/app/js/script.js" defer></script>
    <script>
       document.getElementById('logout-button').addEventListener('click', function() {
    // Afficher le spinner
    const spinner = document.getElementById('loading-spinner');
    spinner.style.display = 'flex';

    // Envoyer une requête AJAX pour déconnecter l'utilisateur
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'logout=true', // Envoyer une donnée POST pour déclencher la déconnexion
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Rafraîchir la page actuelle après la déconnexion
            window.location.reload();
        } else {
            alert('Erreur lors de la déconnexion.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    })
    .finally(() => {
        // Masquer le spinner en cas d'erreur ou de succès
        spinner.style.display = 'none';
    });
});
    </script>
</body>
</html>