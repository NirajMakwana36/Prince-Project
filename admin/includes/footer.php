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

        // Real-time Order Polling
        let lastOrderTime = '<?php 
            $oq = "SELECT MAX(updated_at) as t FROM orders";
            $nq = "SELECT MAX(created_at) as t FROM notifications WHERE role = 'admin'";
            $ot = $conn->query($oq)->fetch_assoc()["t"] ?? '2000-01-01 00:00:00'; 
            $nt = $conn->query($nq)->fetch_assoc()["t"] ?? '2000-01-01 00:00:00'; 
            echo max($ot, $nt) == '2000-01-01 00:00:00' ? date("Y-m-d H:i:s") : max($ot, $nt);
        ?>';
        
        setInterval(() => {
            fetch('<?php echo BASE_URL; ?>api/check_updates.php?last_time=' + encodeURIComponent(lastOrderTime))
            .then(r => r.json())
            .then(data => {
                if (data.has_updates) {
                    lastOrderTime = data.last_time;
                    // Silently fetch the updated page and replace the main content area
                    fetch(location.href)
                    .then(res => res.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        let newMain = doc.querySelector('.admin-main').innerHTML;
                        document.querySelector('.admin-main').innerHTML = newMain;
                    }).catch(err => console.error(err));
                }
            }).catch(e => console.error("Real-time sync error:", e));
        }, 5000);

    </script>
</body>
</html>
