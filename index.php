<?php
// Définir la page courante
$currentPage = 'home';

// Inclure la connexion à la base de données
require_once __DIR__ . '/src/app/config/db.php';

// Initialiser $workers
$workers = [];

try {
    $dbConfig = new DbConfig();
    $pdo = $dbConfig->getConnection();

    // Vérifier si une recherche est soumise
    $activity = filter_input(INPUT_GET, 'activity', FILTER_SANITIZE_STRING) ?? '';
    $city = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_STRING) ?? '';

    // Construire la requête SQL
    $sql = "SELECT * FROM workers INNER JOIN users ON workers.user_id = users.id";

    // Ajouter les conditions de recherche
    $conditions = [];
    if (!empty($activity)) {
        $conditions[] = "workers.profession LIKE :activity";
    }
    if (!empty($city)) {
        $conditions[] = "users.city LIKE :city";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Préparation et exécution
    $stmt = $pdo->prepare($sql);

    if (!empty($activity)) {
        $stmt->bindValue(':activity', '%' . $activity . '%');
    }
    if (!empty($city)) {
        $stmt->bindValue(':city', '%' . $city . '%');
    }

    $stmt->execute();
    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage())); // Sécurisation de l'affichage des erreurs
}

// Configuration des chemins
$uploadDir = __DIR__ . '/src/app/view/uploads/';
$uploadUrl = '/findservice/src/app/view/uploads/';
$defaultImage = 'src/app/view/default.jpg';

// Inclure le header
require_once __DIR__ . '/src/app/component/Header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue dans notre service</title>
    <link rel="stylesheet" href="src/app/style/main.css?v=1.0.1">
    <style>
        :root {
            --primary-color: #ff6347; 
            --primary-hover-color: #ff4500; 
            --secondary-color: #3f3f46;  
            --background-color: #f8fafc;
            --text-color: #1e293b;       
            --light-text-color: #64748b;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Conteneur principal */
        .appointment-container {
            position: fixed;
            left: 0;
            bottom: -100%;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center; 
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            opacity: 0;
        }

        .appointment-container.active {
            bottom: 0;
            opacity: 1;
        }

        /* Carte de rendez-vous */
        .appointment-card {
            position: relative;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            width: 100%;
            max-width: 800px;
            background: white;
            border: none;
            border-radius: 4px 4px;
            box-shadow: var(--shadow);
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
        }

        .appointment-container.active .appointment-card {
            transform: translateY(0);
        }

        /* Image container */
        .image-container {
            height: 240px;
            background-position: center;
            background-size: cover;
            border-radius: 4px 4px 0 0;
            position: relative;
            overflow: hidden;
        }

        .image-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: linear-gradient(transparent, rgba(0,0,0,0.6));
        }

        /* Détails du rendez-vous */
        .appointment-details {
            padding: 2rem;
            overflow-y: auto;
        }

        .worker-info {
            margin-bottom: 2rem;
        }

        .worker-info h2 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .worker-profession {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: capitalize;
        }

        .worker-rate {
            color: var(--light-text-color);
            font-size: 0.9rem;
        }

        /* Formulaire */
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .date-picker-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .date-picker-group label {
            color: var(--text-color);
            font-weight: 500;
            text-transform: capitalize;
        }

        .date-picker-group input[type="date"] {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .date-picker-group input[type="date"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        /* Bouton CTA */
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem 2rem;
            border-radius: 0.75rem;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: fit-content;
        }

        .cta-button:hover {
            background: var(--primary-hover-color);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .cta-button .button-icon {
            transition: transform 0.2s ease;
        }

        .cta-button:hover .button-icon {
            transform: scale(1.1);
        }

        /* Bouton fermeture */
        .btn_close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow);
            transition: all 0.2s ease;
        }

        .btn_close:hover {
            background: white;
            transform: rotate(90deg);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .appointment-card {
                max-width: 100%;
            }
            
            .image-container {
                height: 180px;
            }
            
            .appointment-details {
                padding: 1.5rem;
            }
            
            .cta-button {
                width: 100%;
                justify-content: center;
            }
        }

        @media (min-width: 1024px) {
            .appointment-card {
                flex-direction: row;
                max-height: 600px;
            }
            
            .image-container {
                width: 45%;
                height: auto;
                border-radius: 4px 0 0 4px;
            }
            
            .appointment-details {
                width: 55%;
                padding: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="body_container">

    <!-- Header.php -->
    <header class="hero-section">
        <div class="hero-content">
            <h1>Bienvenue dans notre service</h1>
            <p>Explorez nos fonctionnalités et services qui peuvent vous aider à atteindre vos objectifs.</p>

            <!-- Barre de recherche -->
            <form action="<?php echo getRoute('search'); ?>" method="GET" class="search-form">
                <input type="text" name="activity" placeholder="Rechercher par activité (ex: Jardinage)" required>
                <input type="text" name="city" placeholder="Rechercher par ville" required>
                <button type="submit" class="btn">Rechercher</button>
            </form>
        </div>
    </header>

    <section class="services-section">
        <h1 class="section-title">
            <?= empty($activity) && empty($city) ? 'Notre Équipe' : 'Résultats de la Recherche' ?>
        </h1>

        <?php if (empty($workers)): ?>
            <p class="no-results">Aucun résultat trouvé.</p>
            <div class="empty_">
                <!-- empty  -->
            </div>
        <?php else: ?>
            <div class="services-container">
                <?php foreach ($workers as $worker): 
                    // Génération des chemins d'image
                    $imagePath = $uploadDir . $worker['photo_url'];
                    $imageUrl = file_exists($imagePath) ? $uploadUrl . $worker['photo_url'] : $defaultImage;
                ?>
                    <!-- Carte de worker cliquable -->
                    <div class="service-card" 
                         data-id="<?= htmlspecialchars($worker['id']) ?>" 
                         data-name="<?= htmlspecialchars($worker['first_name'] . ' ' . $worker['last_name']) ?>" 
                         data-profession="<?= htmlspecialchars($worker['profession']) ?>" 
                         data-photo="<?= htmlspecialchars($imageUrl) ?>" 
                         data-rate="<?= htmlspecialchars($worker['hourly_rate']) ?>" 
                         data-description="<?= htmlspecialchars($worker['description']) ?>">
                        
                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                             alt="<?= htmlspecialchars($worker['first_name'] . ' ' . $worker['last_name']) ?>" 
                             class="service-image">

                        <div class="card_text">
                            <div class="card_title">
                                <h2 class="service-title">
                                    <?= htmlspecialchars($worker['first_name'] . ' ' . $worker['last_name']) ?>
                                </h2>
                                <h3 class="service-job-title">
                                    <?= htmlspecialchars($worker['profession']) ?>
                                </h3>
                            </div>
                            <p class="service-description">
                                <?= htmlspecialchars($worker['description']) ?>
                            </p>
                            <p class="service-rate">
                                Tarif Horaire : <?= htmlspecialchars($worker['hourly_rate']) ?> €
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Conteneur de rendez-vous -->
    <div class="appointment-container">
        <div class="appointment-card">
            <button class="btn_close">✖</button>
            <div class="image-container" id="workerImageContainer">
                <!-- Image dynamique -->
            </div>
            
            <div class="appointment-details">
                <div class="form-container">
                    <div class="worker-info">
                        <h2 id="workerName">Chargement...</h2>
                        <p class="worker-profession" id="workerProfession"></p>
                        <p class="worker-rate" id="workerRate"></p>
                        <p class="worker-description" id="workerDescription"></p>
                    </div>
                    
                    <div class="date-picker-group">
                        <label for="appointmentDate">Choisissez une date :</label>
                        <input type="date" 
                               id="appointmentDate" 
                               name="appointmentDate"
                               min="<?= date('Y-m-d') ?>" 
                               required
                               class="date-input">
                        <input type="hidden" id="selectedWorkerId" name="worker_id">
                    </div>
                    
                    <button class="cta-button" onclick="handleAppointment()">
                        <span class="button-icon">📅</span>
                        Prendre Rendez-vous
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
  
    <!-- Script JavaScript -->
    <script>
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('click', (e) => {
            // Ignore les clics sur les liens
            if (!e.target.closest('a')) { 
                const workerData = {
                    id: card.dataset.id,
                    name: card.dataset.name,
                    profession: card.dataset.profession,
                    photo: card.dataset.photo,
                    rate: card.dataset.rate,
                    description: card.dataset.description
                };

                // Mise à jour de l'interface
                document.getElementById('workerImageContainer').style.backgroundImage = `url('${workerData.photo}')`;
                document.getElementById('workerName').textContent = workerData.name;
                document.getElementById('workerProfession').textContent = workerData.profession;
                document.getElementById('workerRate').textContent = `Tarif horaire : ${workerData.rate} €`;
                document.getElementById('workerDescription').textContent = workerData.description;
                document.getElementById('selectedWorkerId').value = workerData.id;

                // Réinitialise la date
                document.getElementById('appointmentDate').value = '';
                
                // Affiche le container avec animation
                document.querySelector('.appointment-container').classList.add('active'); // Assurez-vous que le conteneur est visible
            }
        });
    });

    const toggle_button = document.querySelector('.btn_close');
    const appointmentContainer = document.querySelector('.appointment-container');
    
    if (toggle_button && appointmentContainer) {
        toggle_button.addEventListener('click', () => {
            appointmentContainer.classList.remove('active'); // Masque le conteneur
        });
    }

    // Modification de handleAppointment()
    function handleAppointment() {
        const workerId = document.getElementById('selectedWorkerId').value;
        const dateInput = document.getElementById('appointmentDate');
        const selectedDate = dateInput.value;
        
        if (selectedDate && workerId) {
            // Exemple d'envoi avec Fetch API
            const formData = new FormData();
            formData.append('worker_id', workerId);
            formData.append('date', selectedDate);

            fetch('http://localhost/findservice/src/app/functions/create_appointment.php', { 
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Rendez-vous confirmé avec ${document.getElementById('workerName').textContent} pour le ${selectedDate}`);
                    appointmentContainer.classList.remove('active');
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
            });
        } else {
            alert('Veuillez sélectionner une date valide');
        }
    }
    </script>
</body>
<footer class="footer_b">
    <div class="footer-container">
        <!-- Section À Propos -->
        <div class="footer-section">
            <h3 class="footer-title">À Propos</h3>
            <p class="footer-description">
                Nous sommes dédiés à fournir des services de haute qualité pour vous aider à atteindre vos objectifs.
            </p>
        </div>

        <!-- Liens Utiles -->
        <div class="footer-section">
            <h3 class="footer-title">Liens Pratiques</h3>
            <ul class="footer-links">
                <li><a href="<?php echo getRoute('home'); ?>">Accueil</a></li>
                <li><a href="<?php echo getRoute('explore'); ?>">Services</a></li>
                <li><a href="<?php echo getRoute('about'); ?>">À Propos</a></li>
                <li><a href="<?php echo getRoute('contact'); ?>">Contact</a></li>
                <li><a href="<?php echo getRoute('login'); ?>">Connexion</a></li>
            </ul>
        </div>

        <!-- Informations de Contact -->
        <div class="footer-section">
            <h3 class="footer-title">Contactez-Nous</h3>
            <ul class="footer-contact">
                <li><i class="fas fa-map-marker-alt"></i> 123 Main Street, City, Country</li>
                <li><i class="fas fa-phone"></i> +123 456 7890</li>
                <li><i class="fas fa-envelope"></i> info@example.com</li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-section">
            <h3 class="footer-title">Bulletin d'Information</h3>
            <p class="footer-description">
                Abonnez-vous à notre newsletter pour recevoir les dernières mises à jour et offres.
            </p>
            <form class="newsletter-form">
                <input type="email" placeholder="Entrez votre email" required>
                <button type="submit" class="btn">S'abonner</button>
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
        <p>&copy; 2025 Votre Entreprise. Tous droits réservés.</p>
    </div>
</footer>

</html>
