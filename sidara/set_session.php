<?php
session_start();
if (isset($_POST['file_id'])) {
    $_SESSION['valid_download'] = true;
    $_SESSION['file_id'] = intval($_POST['file_id']); // Simpan file_id dalam sesi
    header("Location: unduh.php");
    exit();
} else {
    echo "Akses tidak sah!";
    exit();
}
?>
