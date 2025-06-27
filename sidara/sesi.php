<?php 
$loggedIn = isset($_SESSION['authorized']) && $_SESSION['authorized'] === true;
if ($loggedIn): // Hanya jalankan script jika pengguna login ?>
    <script>
        let timeout;
        const logoutTime = 600000; // 1 detik = 1000

        function startLogoutTimer() {
            clearTimeout(timeout);
            console.log("Mulai timer logout...");
            timeout = setTimeout(logoutUser, logoutTime);
        }

        function resetTimer() {
            clearTimeout(timeout);
            console.log("Timer logout dibatalkan.");
        }

        function logoutUser() {
            console.log("Melakukan logout...");
            fetch('logout.php')
                .then(response => {
                    window.location.href = 'home.php';
                })
                .catch(error => console.error('Error:', error));
        }

        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                console.log("Pengguna berpindah tab, mulai timer logout...");
                startLogoutTimer();
            } else {
                console.log("Pengguna kembali ke tab, batalkan logout.");
                resetTimer();
            }
        });

        window.onload = resetTimer;
    </script>
<?php endif; ?>