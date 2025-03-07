<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../functions/routes.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (isset($_SESSION['user_token'])) {
        unset($_SESSION['user_token']);
    }

    session_unset();
    session_destroy();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    exit();
}

$currentPage = 'explore'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/app/style/header.css?v=1.0.1">
    <title>En-tête</title>
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
                <a href="<?php echo getRoute('explore'); ?>" class="nav-link <?php echo ($currentPage == 'explore') ? 'active' : ''; ?>">Explorer</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo getRoute('about'); ?>" class="nav-link <?php echo ($currentPage == 'findservice/about') ? 'active' : ''; ?>">À propos</a>
            </li>
            <li class="nav-item">
                <a href="<?php echo getRoute('contact'); ?>" class="nav-link <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">Contactez</a>
            </li>
        </ul>
        <div class="login_container">
            <?php if (isLoggedIn()): ?>
                <button id="logout-button" class="logout-button">Déconnexion</button>
            <?php else: ?>
                <a href="<?php echo getRoute('login'); ?>" >Connexion</a>
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
        const spinner = document.getElementById('loading-spinner');
        spinner.style.display = 'flex';
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'logout=true',
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert('Erreur lors de la déconnexion.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        })
        .finally(() => {
            spinner.style.display = 'none';
        });
    });
</script>
</body>
</html>
