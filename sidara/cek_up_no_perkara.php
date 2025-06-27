<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

header('Content-Type: application/json');

if (isset($_POST['ID']) && isset($_POST['NO_PERKARA'])) {
    $id = trim($_POST['ID']);
    $no_perkara = trim($_POST['NO_PERKARA']);
    $tahun_perkara = isset($_POST['TAHUN_PERKARA']) ? trim($_POST['TAHUN_PERKARA']) : null;

    // Jika tahun perkara kosong, langsung lolos tanpa validasi
    if (empty($tahun_perkara)) {
        echo json_encode(["exists" => false, "message" => "Tahun perkara kosong, validasi dilewati."]);
        exit;
    }

    // Ambil data lama
    $query_lama = "SELECT NO_PERKARA, TAHUN_PERKARA FROM data_ac WHERE ID = ?";
    $stmt_lama = $conn->prepare($query_lama);
    $stmt_lama->bind_param("i", $id);
    $stmt_lama->execute();
    $result_lama = $stmt_lama->get_result();
    $data_lama = $result_lama->fetch_assoc();
    $stmt_lama->close();

    if ($data_lama) {
        if ($no_perkara === $data_lama['NO_PERKARA'] && $tahun_perkara == $data_lama['TAHUN_PERKARA']) {
            echo json_encode(["exists" => false, "message" => "Data NO_PERKARA tidak berubah"]);
            exit;
        }
    }

    // Cek NO_PERKARA di tahun yang sama
    $query = "SELECT COUNT(*) as count FROM data_ac WHERE NO_PERKARA = ? AND TAHUN_PERKARA = ? AND ID != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $no_perkara, $tahun_perkara, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['count'] > 0) {
        echo json_encode(["exists" => true, "message" => "NO PERKARA sudah terdaftar pada tahun $tahun_perkara"]);
    } else {
        echo json_encode(["exists" => false, "message" => "NO PERKARA belum terdaftar untuk tahun $tahun_perkara"]);
    }
} else {
    echo json_encode(["error" => "Data tidak lengkap"]);
}

$conn->close();
?>
