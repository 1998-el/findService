<?php
// Register.php

session_start();

include_once '../config/DbConfig.php'; // Inclure la configuration de la base de données

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture des données du formulaire
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'client'; // Par défaut, le rôle est 'client'
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';

    // Validation des données
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($city)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        // Vérifier si l'email est déjà utilisé
        $checkEmail = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->execute([$email]);

        if ($checkEmail->rowCount() > 0) {
            $error = 'Cet email est déjà enregistré. Veuillez utiliser un autre email.';
        } else {
            // Hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insérer l'utilisateur dans la table `users`
            try {
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address, city, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone, $address, $city, $role]);

                // Récupérer l'ID de l'utilisateur nouvellement inscrit
                $userId = $pdo->lastInsertId();

                // Simuler la connexion
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $firstName;
                $_SESSION['role'] = $role;

                // Rediriger en fonction du rôle
                if ($role === 'worker') {
                    // Rediriger vers le formulaire supplémentaire pour les Workers
                    header('Location: worker_registration.php?user_id=' . $userId);
                    exit;
                } else {
                    // Rediriger vers la page d'accueil pour les Clients
                    header('Location: index.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de l\'inscription : ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../style/Register.css?v=1.0.1">
</head>
<body>
    <div class="register-container">
        <div class="image_left">
            <!-- Image de fond pour la section gauche -->
        </div>
        <div class="form_container">
            <form action="Register.php" method="POST">
                <h2>Inscription</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <div class="input-field">
                    <div class="input-group">
                        <label for="first_name">Prénom</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="input-group">
                        <label for="last_name">Nom de famille</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="input-field">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="input-field">
                    <div class="input-group">
                        <label for="phone">Téléphone</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="input-group">
                        <label for="address">Adresse</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                </div>
                <div class="input-field">
                    <div class="input-group">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="input-group">
                        <label for="role">Rôle</label>
                        <select id="role" name="role">
                            <option value="client">Client</option>
                            <option value="worker">Worker</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
        </div>
    </div>
</body>
</html>