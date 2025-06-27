<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';

header('Content-Type: application/json');

if (isset($_POST['ID']) && isset($_POST['NO_AC']) && isset($_POST['TANGGAL_AC_M'])) {
    $id = trim($_POST['ID']);
    $no_ac = trim($_POST['NO_AC']);
    $tanggal_ac_m = trim($_POST['TANGGAL_AC_M']);

    // Validasi format tanggal
    if (!strtotime($tanggal_ac_m)) {
        echo json_encode(["error" => "Format tanggal tidak valid"]);
        exit;
    }

    // Ambil tahun dari tanggal
    $tahun_ac = date('Y', strtotime($tanggal_ac_m));

    // Ambil data lama dari database
    $query_lama = "SELECT NO_AC, YEAR(TANGGAL_AC_M) as TAHUN_AC FROM data_ac WHERE ID = ?";
    $stmt_lama = $conn->prepare($query_lama);
    $stmt_lama->bind_param("i", $id);
    $stmt_lama->execute();
    $result_lama = $stmt_lama->get_result();
    $data_lama = $result_lama->fetch_assoc();
    $stmt_lama->close();

    if ($data_lama) {
        $no_ac_lama = $data_lama['NO_AC'];
        $tahun_ac_lama = $data_lama['TAHUN_AC'];

        // Jika NO_AC dan Tahun tidak berubah, izinkan penyimpanan
        if ($no_ac === $no_ac_lama && $tahun_ac == $tahun_ac_lama) {
            echo json_encode(["exists" => false, "message" => "Data tidak berubah, aman untuk disimpan"]);
            exit;
        }
    }

    // Cek apakah NO_AC sudah ada di tahun yang sama (tanpa ID yang sedang diedit)
    $query = "SELECT COUNT(*) as count FROM data_ac WHERE NO_AC = ? AND YEAR(TANGGAL_AC_M) = ? AND ID != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $no_ac, $tahun_ac, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['count'] > 0) {
        echo json_encode([
            "exists" => true, 
            "message" => "NO AC sudah terdaftar pada tahun $tahun_ac"
        ]);
    } else {
        echo json_encode([
            "exists" => false, 
            "message" => "NO AC belum terdaftar untuk tahun $tahun_ac"
        ]);
    }
} else {
    echo json_encode(["error" => "ID, NO_AC, atau TANGGAL_AC_M tidak diterima"]);
}

$conn->close();
?>
