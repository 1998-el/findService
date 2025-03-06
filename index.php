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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Service</title>
    <link rel="stylesheet" href="src/app/style/main.css?v=1.0.1"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <div class="body_container">

    <!-- Header.php -->
    <header class="hero-section">
        <div class="hero-content">
            <h1>Welcome to Our Service</h1>
            <p>Explore our features and services that can help you achieve your goals.</p>

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
            <?= empty($activity) && empty($city) ? 'Our Team' : 'Search Results' ?>
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
                            Tarif horaire : <?= htmlspecialchars($worker['hourly_rate']) ?> €
                        </p>
                        <!-- Lien "Learn More" avec ID worker -->
                        <a href="<?= getRoute('preview') ?>?worker_id=<?= htmlspecialchars($worker['id']) ?>" 
                           class="btn">
                            Learn More
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Conteneur de rendez-vous -->
    <div class="appointment-container">
        <div class="appointment-card">
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
    // Gestion du clic sur les cartes
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
                
                // Affiche le container
                document.querySelector('.appointment-container').classList.add('active'); // Assurez-vous que le conteneur est visible
            }
        });
    });

    // Gestion de la fermeture
    const toggle_button = document.querySelector('.btn_close');
    const appointmentContainer = document.querySelector('.appointment-container');
    
    if (toggle_button && appointmentContainer) {
        toggle_button.addEventListener('click', () => {
            appointmentContainer.classList.remove('active');
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

            fetch('<?= getRoute("appointment") ?>', {
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

</html>