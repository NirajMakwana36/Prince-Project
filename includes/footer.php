    </div> <!-- End page-wrapper -->

    <footer class="footer" style="background-color: var(--secondary); color: white; padding: 5rem 0 2rem; margin-top: 5rem;">
        <div class="container">
            <div class="footer-grid" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 3rem; margin-bottom: 4rem;">
                <div class="footer-about">
                    <a href="<?php echo BASE_URL; ?>" class="logo" style="color: white; margin-bottom: 1.5rem; display: inline-flex;">
                        <i class="fas fa-shopping-basket"></i>
                        <span>CoGroCart</span>
                    </a>
                    <p style="color: #cbd5e1; margin-bottom: 2rem; max-width: 300px;">
                        Pioneering the future of fresh grocery delivery with speed, quality, and a touch of love.
                    </p>
                    <div class="social-links" style="display: flex; gap: 1rem;">
                        <a href="#" style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: white; transition: var(--transition);"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: white; transition: var(--transition);"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: white; transition: var(--transition);"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4 style="color: white; margin-bottom: 1.5rem; font-family: 'Outfit', sans-serif;">Shop</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo BASE_URL; ?>customer/products.php" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">All Products</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo BASE_URL; ?>#categories" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Categories</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Special Offers</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Trending</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 style="color: white; margin-bottom: 1.5rem; font-family: 'Outfit', sans-serif;">Support</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Help Center</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Privacy Policy</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Terms of Service</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="#" style="color: #cbd5e1; text-decoration: none; transition: var(--transition);">Returns</a></li>
                    </ul>
                </div>

                <div class="footer-newsletter">
                    <h4 style="color: white; margin-bottom: 1.5rem; font-family: 'Outfit', sans-serif;">Newsletter</h4>
                    <p style="color: #cbd5e1; margin-bottom: 1.5rem;">Get updates on new products and special offers.</p>
                    <div style="display: flex; gap: 0.5rem; background: rgba(255,255,255,0.05); padding: 0.5rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                        <input type="email" placeholder="Email address" style="flex: 1; background: transparent; border: none; color: white; padding-left: 1rem; outline: none;">
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem;">Join</button>
                    </div>
                </div>
            </div>

            <div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; display: flex; justify-content: space-between; align-items: center; color: #94a3b8; font-size: 0.9rem;">
                <p>&copy; <?php echo date('Y'); ?> CoGroCart. All rights reserved.</p>
                <div style="display: flex; gap: 2rem;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" style="height: 1.5rem; opacity: 0.5; filter: grayscale(1);">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" style="height: 1.5rem; opacity: 0.5; filter: grayscale(1);">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height: 1.5rem; opacity: 0.5; filter: grayscale(1);">
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
    <style>
        .footer-links a:hover {
            color: var(--primary) !important;
            padding-left: 5px;
        }
        .social-links a:hover {
            background: var(--primary) !important;
            transform: translateY(-3px);
        }
        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }
        }
    </style>
</body>
</html>
