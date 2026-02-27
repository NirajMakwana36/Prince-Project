    </div> <!-- End page-wrapper -->

    </div> <!-- End page-wrapper -->

    <footer style="background: var(--secondary-light); color: white; padding: 6rem 0 3rem; margin-top: 5rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 4rem; margin-bottom: 5rem;">
                <div>
                    <a href="<?php echo BASE_URL; ?>" class="logo" style="margin-bottom: 1.5rem; display: inline-flex;">
                        <i class="fas fa-shopping-basket"></i>
                        <span style="color: white;">CoGroCart</span>
                    </a>
                    <p style="color: var(--nav-text); margin-bottom: 2rem; max-width: 320px; font-size: 0.95rem; line-height: 1.8;">
                        Revolutionizing grocery shopping with a premium digital experience and lightning-fast logistics.
                    </p>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: 2rem; font-size: 1.1rem; letter-spacing: 1px; text-transform: uppercase;">Discover</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 1rem;"><a href="<?php echo BASE_URL; ?>customer/products.php" class="footer-link">Fresh Produce</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Organic Selection</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Beverage Cellar</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Daily Essentials</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: 2rem; font-size: 1.1rem; letter-spacing: 1px; text-transform: uppercase;">Company</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Our Story</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Partner with Us</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Sustainability</a></li>
                        <li style="margin-bottom: 1rem;"><a href="#" class="footer-link">Careers</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: 2rem; font-size: 1.1rem; letter-spacing: 1px; text-transform: uppercase;">Support</h4>
                    <p style="color: var(--nav-text); margin-bottom: 1.5rem; font-size: 0.9rem;">Need help with your order?</p>
                    <a href="mailto:support@cogrocart.com" style="color: var(--primary); text-decoration: none; font-weight: 800; font-size: 1.1rem; display: block; margin-bottom: 2rem;">support@cogrocart.com</a>
                    <div style="padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.05); font-size: 0.8rem; color: var(--nav-text);">
                        <i class="fas fa-clock" style="margin-right: 0.5rem; color: var(--primary);"></i> 24/7 Premium Support
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 2.5rem; display: flex; justify-content: space-between; align-items: center; color: var(--nav-text); font-size: 0.85rem; font-weight: 600;">
                <p>&copy; <?php echo date('Y'); ?> CoGroCart. Elevating Your Lifestyle.</p>
                <div style="display: flex; gap: 2.5rem;">
                    <a href="#" style="color: inherit; text-decoration: none;">Privacy</a>
                    <a href="#" style="color: inherit; text-decoration: none;">Terms</a>
                    <a href="#" style="color: inherit; text-decoration: none;">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .footer-link { color: var(--nav-text); text-decoration: none; transition: 0.3s; font-size: 0.95rem; font-weight: 500; }
        .footer-link:hover { color: var(--primary); transform: translateX(5px); display: inline-block; }
        
        .social-icon { width: 3rem; height: 3rem; border-radius: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.3s; font-size: 1.25rem; }
        .social-icon:hover { background: var(--primary); color: var(--secondary); transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.3); }

        @media (max-width: 900px) {
            footer > .container > div:first-child { grid-template-columns: 1fr 1fr !important; gap: 3rem !important; }
        }
        @media (max-width: 600px) {
            footer > .container > div:first-child { grid-template-columns: 1fr !important; }
            footer > .container > div:last-child { flex-direction: column; gap: 1.5rem; text-align: center; }
        }
    </style>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
</body>
</html>
