<footer class="footer">
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
        <p>&copy; 2023 Your Company. All rights reserved.</p>
    </div>
</footer>