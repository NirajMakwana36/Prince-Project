        </main>
    </div>
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
    <script>
        // Admin specific JS
        function confirmAction(e, msg = 'Are you sure you want to proceed?') {
            if (!confirm(msg)) {
                e.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
