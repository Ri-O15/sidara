<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php'; // Pastikan koneksi benar

header('Content-Type: application/json'); // Pastikan response selalu JSON

if (isset($_POST['NO_PERKARA']) && isset($_POST['TAHUN_PERKARA'])) {
    $no_perkara = $_POST['NO_PERKARA'];
    $tahun_perkara = $_POST['TAHUN_PERKARA'];

    // Cek apakah kombinasi NO_PERKARA dan TAHUN_PERKARA sudah ada
    $query = "SELECT COUNT(*) as jumlah FROM data_ac WHERE NO_PERKARA = ? AND TAHUN_PERKARA = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["error" => "Query error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("ii", $no_perkara, $tahun_perkara);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['jumlah'] > 0) {
        // Jika kombinasi sudah ada
        echo json_encode([
            "exists" => true,
            "message" => "No Perkara $no_perkara sudah terdaftar pada tahun $tahun_perkara. Nomor perkara tidak boleh sama dalam tahun yang sama."
        ]);
    } else {
        // Jika kombinasi belum ada, izinkan penyimpanan
        echo json_encode(["exists" => false, "message" => "No Perkara dapat digunakan."]);
    }
} else {
    echo json_encode(["error" => "NO_PERKARA atau TAHUN_PERKARA tidak diterima"]);
}
?>
