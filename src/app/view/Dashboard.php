<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Prestataire de Services</title>
    <link rel="stylesheet" href="../style/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Pour les graphiques -->
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
                    <li><a href="#" class="nav-link active" data-section="home"><i class="icon-home"></i> Accueil</a></li>
                    <li><a href="#" class="nav-link" data-section="appointments"><i class="icon-calendar"></i> Rendez-vous</a></li>
                    <li><a href="#" class="nav-link" data-section="tasks"><i class="icon-tasks"></i> Tâches</a></li>
                    <li><a href="#" class="nav-link" data-section="revenue"><i class="icon-money"></i> Revenus</a></li>
                    <li><a href="#" class="nav-link" data-section="settings"><i class="icon-settings"></i> Paramètres</a></li>
                </ul>
            </nav>
            <div class="profile">
                <img src="avatar.jpg" alt="Avatar">
                <span>John Doe</span>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Section Accueil (par défaut) -->
            <section id="home" class="content-section active">
                <!-- KPI Cards -->
                <section class="kpi-cards">
                    <div class="card">
                        <h3>Rendez-vous Aujourd'hui</h3>
                        <p>5</p>
                    </div>
                    <div class="card">
                        <h3>Tâches en Attente</h3>
                        <p>3</p>
                    </div>
                    <div class="card">
                        <h3>Revenus du Mois</h3>
                        <p>$2,500</p>
                    </div>
                    <div class="card">
                        <h3>Note Moyenne</h3>
                        <p>4.7/5</p>
                    </div>
                </section>

                <!-- Calendrier des Rendez-vous -->
                <section class="calendar">
                    <h3>Calendrier des Rendez-vous</h3>
                    <div id="calendar"></div> <!-- Intégrez un plugin de calendrier ici -->
                </section>

                <!-- Liste des Tâches -->
                <section class="tasks">
                    <h3>Tâches à Accomplir</h3>
                    <ul>
                        <li>
                            <span class="task-name">Nettoyage complet</span>
                            <span class="task-priority high">Haute Priorité</span>
                        </li>
                        <li>
                            <span class="task-name">Réparation plomberie</span>
                            <span class="task-priority medium">Moyenne Priorité</span>
                        </li>
                    </ul>
                </section>

                <!-- Graphiques -->
                <section class="graphs">
                    <canvas id="revenueChart"></canvas>
                    <canvas id="appointmentsChart"></canvas>
                </section>
            </section>

            <!-- Section Rendez-vous -->
            <section id="appointments" class="content-section">
                <h2>Rendez-vous</h2>
                <div class="appointments-list">
                    <div class="appointment">
                        <h3>Nettoyage de bureau</h3>
                        <p><strong>Date :</strong> 2023-10-15</p>
                        <p><strong>Heure :</strong> 10:00 - 12:00</p>
                        <p><strong>Client :</strong> Jane Doe</p>
                        <p><strong>Téléphone :</strong> +123 456 789</p>
                        <p><strong>Adresse :</strong> 123 Rue Principale, Ville</p>
                        <p><strong>Localisation :</strong> <a href="https://www.google.com/maps?q=123+Rue+Principale,Ville" target="_blank">Voir sur la carte</a></p>
                    </div>
                    <div class="appointment">
                        <h3>Réparation plomberie</h3>
                        <p><strong>Date :</strong> 2023-10-16</p>
                        <p><strong>Heure :</strong> 14:00 - 16:00</p>
                        <p><strong>Client :</strong> John Smith</p>
                        <p><strong>Téléphone :</strong> +987 654 321</p>
                        <p><strong>Adresse :</strong> 456 Avenue Secondaire, Ville</p>
                        <p><strong>Localisation :</strong> <a href="https://www.google.com/maps?q=456+Avenue+Secondaire,Ville" target="_blank">Voir sur la carte</a></p>
                    </div>
                </div>
            </section>

            <!-- Section Tâches -->
            <section id="tasks" class="content-section">
                <h2>Tâches</h2>
                <div class="tasks-list">
                    <div class="task">
                        <h3>Nettoyage complet</h3>
                        <p><strong>Priorité :</strong> Haute</p>
                        <p><strong>Statut :</strong> En attente</p>
                    </div>
                    <div class="task">
                        <h3>Réparation plomberie</h3>
                        <p><strong>Priorité :</strong> Moyenne</p>
                        <p><strong>Statut :</strong> En cours</p>
                    </div>
                </div>
            </section>

            <!-- Section Revenus -->
            <section id="revenue" class="content-section">
                <h2>Revenus</h2>
                <div class="revenue-stats">
                    <div class="stat">
                        <h3>Revenus du Mois</h3>
                        <p>$2,500</p>
                    </div>
                    <div class="stat">
                        <h3>Revenus de l'Année</h3>
                        <p>$30,000</p>
                    </div>
                </div>
                <canvas id="revenueChart"></canvas>
            </section>

            <!-- Section Paramètres -->
            <section id="settings" class="content-section">
                <h2>Paramètres</h2>
                <form class="settings-form">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" value="John Doe">

                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="john.doe@example.com">

                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">

                    <button type="submit">Enregistrer</button>
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