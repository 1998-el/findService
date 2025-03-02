<?php
// login.php

session_start();

// Database connection parameters
$host = '127.0.0.1';
$db = 'services';
$user = 'root'; // Default user for local development
$pass = ''; // Default password for local development
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

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture des données du formulaire
    $email = $_POST['username'] ?? ''; // Utilisez 'email' au lieu de 'username' si votre champ est un email
    $password = $_POST['password'] ?? '';

    // Validation des données
    if (empty($email) || empty($password)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        // Récupérer l'utilisateur par son email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['first_name']; // Stocker le prénom de l'utilisateur
            $_SESSION['role'] = $user['role']; // Stocker le rôle de l'utilisateur

            // Rediriger en fonction du rôle
            if ($user['role'] === 'worker') {
                header('Location: worker_dashboard.php');
            } else {
                header('Location: /findservice/');
            }
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../style/login.css">
</head>
<body>
    <div class="login-container">
        <div class="image_left">
            <!-- Image de fond pour la section gauche -->
        </div>
        <div class="form_left">
            <form action="login.php" method="POST">
                <h2>Connexion</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <div class="input-group">
                    <label for="username">Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    <div class="forgot_pass"><a href="#">Mot de passe oublié ?</a></div>
                </div>
                <button type="submit" class="btn">Se connecter</button>
                <div class="login_footer">
                    <span>Vous n'avez pas de compte ?</span>
                    <a href="register.php">S'inscrire</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>