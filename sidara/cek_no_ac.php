<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

header('Content-Type: application/json');

if (isset($_POST['NO_AC']) && isset($_POST['TANGGAL_AC_M'])) {
    $no_ac = trim($_POST['NO_AC']);
    $tanggal_ac_m = trim($_POST['TANGGAL_AC_M']);

    // Debug: Cek format tanggal yang diterima
    error_log("NO_AC: " . $no_ac);
    error_log("TANGGAL_AC_M: " . $tanggal_ac_m);

    // Validasi format tanggal
    if (!strtotime($tanggal_ac_m)) {
        echo json_encode(["error" => "Format tanggal tidak valid"]);
        exit;
    }

    // Ambil tahun dari tanggal
    $tahun_ac = date('Y', strtotime($tanggal_ac_m));
    error_log("Tahun AC: " . $tahun_ac);

    // Query cek NO_AC dalam tahun yang sama
    $query = "SELECT COUNT(*) as count FROM data_ac WHERE NO_AC = ? AND YEAR(TANGGAL_AC_M) = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["error" => "Query error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $no_ac, $tahun_ac);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(["exists" => true, "message" => "NO AC sudah ada untuk tahun $tahun_ac"]);
    } else {
        echo json_encode(["exists" => false, "message" => "NO AC belum terdaftar untuk tahun $tahun_ac"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "NO_AC atau TANGGAL_AC_M tidak diterima"]);
}

$conn->close();
?>
