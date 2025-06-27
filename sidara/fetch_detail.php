<?php
include 'koneksi.php';

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]); // Keamanan: Pastikan ID angka

    $stmt = $conn->prepare("SELECT * FROM data_ac WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "NO_AC" => $data["NO_AC"],
            "TAHUN_AC" => $data["TAHUN_AC"],
            "HARI_AC" => $data["HARI_AC"],
            "TANGGAL_AC_M" => $data["TANGGAL_AC_M"],
            "NO_PERKARA" => $data["NO_PERKARA"],
            "TAHUN_PERKARA" => $data["TAHUN_PERKARA"],
            "TANGGAL_PUTUS" => $data["TANGGAL_PUTUS"],
            "NAMA_P" => $data["NAMA_P"],
            "UMUR_P" => $data["UMUR_P"],
            "BINTI_P" => $data["BINTI_P"],
            "PEKERJAAN_P" => $data["PEKERJAAN_P"],
            "ALAMAT_P" => $data["ALAMAT_P"],
            "ALAMAT_P_RT_RW" => $data["ALAMAT_P_RT_RW"],
            "ALAMAT_P_KELURAHAN" => $data["ALAMAT_P_KELURAHAN"],
            "KECAMATAN_P" => $data["KECAMATAN_P"],
            "KABUPATEN_KOTA_P" => $data["KABUPATEN_KOTA_P"],
            "NAMA_T" => $data["NAMA_T"],
            "UMUR_T" => $data["UMUR_T"],
            "BIN_T" => $data["BIN_T"],
            "PEKERJAAN_T" => $data["PEKERJAAN_T"],
            "ALAMAT_T" => $data["ALAMAT_T"],
            "ALAMAT_T_RT_RW" => $data["ALAMAT_T_RT_RW"],
            "ALAMAT_T_KELURAHAN" => $data["ALAMAT_T_KELURAHAN"],
            "KECAMATAN_T" => $data["KECAMATAN_T"],
            "KABUPATEN_KOTA_T" => $data["KABUPATEN_KOTA_T"],
            "KUA_KECAMATAN" => $data["KUA_KECAMATAN"],
            "KABUPATEN_KOTA_KUA" => $data["KABUPATEN_KOTA_KUA"],
            "TANGGAL_NIKAH" => $data["TANGGAL_NIKAH"],
            "NO_AKTANIKAH" => $data["NO_AKTANIKAH"],
            "FILE_NAME" => $data["FILE_NAME"],
            "ID" => $id
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
