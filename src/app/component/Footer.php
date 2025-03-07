<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3 class="footer-title">À Propos</h3>
            <p class="footer-description">
                Nous nous engageons à fournir des services de haute qualité pour vous aider à atteindre vos objectifs.
            </p>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Liens Pratiques</h3>
            <ul class="footer-links">
                <li><a href="<?php echo getRoute('home'); ?>">Accueil</a></li>
                <li><a href="<?php echo getRoute('explore'); ?>">Services</a></li>
                <li><a href="<?php echo getRoute('about'); ?>">À Propos</a></li>
                <li><a href="<?php echo getRoute('contact'); ?>">Contactez</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Contact</h3>
            <ul class="footer-contact">
                <li><i class="fas fa-map-marker-alt"></i> 123 Main Street, City, Country</li>
                <li><i class="fas fa-phone"></i> +123 456 7890</li>
                <li><i class="fas fa-envelope"></i> info@example.com</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Bulletin d'Information</h3>
            <p class="footer-description">
                Abonnez-vous à notre newsletter pour recevoir les dernières mises à jour et offres.
            </p>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email" required>
                <button type="submit" class="btn">S'abonner</button>
            </form>
        </div>
    </div>
    <div class="footer-social">
        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2023 Votre Entreprise. Tous droits réservés.</p>
    </div>
</footer>
