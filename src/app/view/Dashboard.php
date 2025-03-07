<?php
require_once __DIR__ . '/../config/db.php';

// Connexion à la base de données
$dbConfig = new DbConfig();
$pdo = $dbConfig->getConnection();

try {
    // Récupérer les rendez-vous avec les informations des clients
    $sql = "SELECT s.id, s.appointment_date, u.first_name, u.last_name, w.hourly_rate, u.phone, u.address 
            FROM services s 
            JOIN workers w ON s.worker_id = w.id 
            JOIN users u ON w.user_id = u.id 
            ORDER BY s.appointment_date DESC";
        
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compter le nombre total de rendez-vous
    $stmt_count = $pdo->prepare("SELECT COUNT(*) AS total_appointments FROM services");
    $stmt_count->execute();
    
    // Récupérer le résultat du comptage
    $result = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $totalAppointments = $result['total_appointments'];
    
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Service</title>

    <link rel="stylesheet" href="../style/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #ff6347; /* Couleur principale (orange) */
            --primary-hover-color: #ff4500; 
            --secondary-color: #3f3f46;
            --background-color: #f8fafc;
            --text-color: #1e293b;
            --light-text-color: #64748b;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Layout */
        .dashboard {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            background: var(--background-color);
        }

        .sidebar {
            background: #121212;
            box-shadow: var(--shadow);
            padding: 2rem;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        /* Appointments List */
        .appointments-list {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .appointment {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.2s ease;
        }

        .appointment:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animated {
            animation: fadeIn 0.4s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }

            .sidebar {
                height: auto;
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>H<span>service</span></h1>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="#" class="nav-link active" data-section="home">Accueil</a></li>
                    <li><a href="#" class="nav-link" data-section="appointments">Rendez-vous</a></li>
                    <li><a href="#" class="nav-link" data-section="tasks">Tâches</a></li>
                    <li><a href="#" class="nav-link" data-section="revenue">Revenus</a></li>
                    <li><a href="#" class="nav-link" data-section="settings">Paramètres</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Section Accueil -->
            <section id="home" class="content-section active animated">
                <div class="kpi-cards">
                    <div class="card">
                        <h3>Rendez-vous d'Aujourd'hui</h3>
                        <p><?php echo htmlspecialchars($totalAppointments) ?></p>
                    </div>
                    <div class="card">
                        <h3>Tâches en Attente</h3>
                        <p>3</p>
                    </div>
                    <div class="card">
                        <h3>Revenus Mensuels</h3>
                        <p>0,000 FCFA</p>
                    </div>
                    <div class="card">
                        <h3>Évaluation Moyenne</h3>
                        <p>4.7/5</p>
                    </div>
                </div>
            </section>

            <!-- Section Rendez-vous -->
            <section id="appointments" class="content-section">
                <h2>Rendez-vous</h2>
                <div class="appointments-list">
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <div class="appointment animated">
                                <h3><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></h3>
                                <p><strong>Date :</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></p>
                                <p><strong>Heure :</strong> <?= htmlspecialchars($appointment['hourly_rate']) ?> €</p>
                                <p><strong>Client :</strong> <?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></p>
                                <p><strong>Téléphone :</strong> <?= htmlspecialchars($appointment['phone']) ?></p>
                                <p><strong>Adresse :</strong> <?= htmlspecialchars($appointment['address']) ?></p>
                                <p><strong>Localisation :</strong> <a href="https://www.google.com/maps?q=<?= urlencode($appointment['address']) ?>" target="_blank">Voir sur la carte</a></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun rendez-vous trouvé.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Section Tâches -->
            <section id="tasks" class="content-section">
                <h2>Tâches à Réaliser</h2>
                <div class="tasks-list">
                    <div class="task card animated">
                        <h3>Nettoyage complet</h3>
                        <p><strong>Priorité :</strong> Haute</p>
                        <p><strong>Statut :</strong> En attente</p>
                    </div>
                    <div class="task card animated">
                        <h3>Réparation plomberie</h3>
                        <p><strong>Priorité :</strong> Moyenne</p>
                        <p><strong>Statut :</strong> En cours</p>
                    </div>
                </div>
            </section>

            <!-- Section Revenus -->
            <section id="revenue" class="content-section">
                <h2>Revenus Mensuels</h2>
                <div class="revenue-stats">
                    <div class="stat card animated">
                        <h3>Revenus Mensuels</h3>
                        <p>$2,500</p>
                    </div>
                    <div class="stat card animated">
                        <h3>Revenus de l'Année</h3>
                        <p>$30,000</p>
                    </div>
                </div>
                <canvas id="revenueChart"></canvas>
            </section>

            <!-- Section Paramètres -->
            <section id="settings" class="content-section">
                <h2>Paramètres</h2>
                <form class="settings-form card animated">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur :</label>
                        <input type="text" id="username" name="username" value="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" value="john.doe@example.com">
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">
                    </div>
                    <button type="submit" class="btn">Enregistrer</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        // Gestion du changement de section
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Masquer toutes les sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });

                // Afficher la section correspondante
                const targetSection = this.getAttribute('data-section');
                document.getElementById(targetSection).classList.add('active');

                // Mettre à jour le lien actif dans la sidebar
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
