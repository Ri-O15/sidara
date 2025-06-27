<?php
session_start();
include 'koneksi.php';

// Cek apakah sesi valid dan file_id tersimpan
if (!isset($_SESSION['valid_download']) || $_SESSION['valid_download'] !== true || !isset($_SESSION['file_id'])) {
    echo "Akses tidak diizinkan!";
    exit();
}

$id = $_SESSION['file_id']; // Gunakan file_id yang tersimpan dalam sesi

// Ambil data file dari database
$query = "SELECT FILE_NAME, FILE_TYPE, FILE_DATA FROM data_ac WHERE ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();

// Jika file ditemukan
if ($stmt->num_rows > 0) {
    $stmt->bind_result($file_name, $file_type, $file_content);
    $stmt->fetch();

    // Pastikan file adalah PDF
    if ($file_type !== 'application/pdf') {
        echo "Format file tidak didukung!";
        exit();
    }

    // Set header untuk menampilkan PDF di browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $file_name . '"');
    header('Content-Length: ' . strlen($file_content));

    // Output file ke browser
    echo $file_content;
} else {
    echo "File tidak ditemukan!";
}

$stmt->close();
$conn->close();
?>
