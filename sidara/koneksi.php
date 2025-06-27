<?php
$host = "localhost";  // Server Database
$user = "root";       // Username MySQL (default root)
$pass = "";           // Password MySQL (kosongkan jika default)
$db = "data_pa";  // Nama Database

$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
