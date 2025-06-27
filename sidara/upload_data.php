<?php
include 'koneksi.php'; // Panggil file koneksi

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $no_ac = $_POST["NO_AC"];
    $tanggal_ac_m = $_POST['TANGGAL_AC_M'] ?? NULL;
        if ($tanggal_ac_m === NULL || $tanggal_ac_m === '') {
            $tanggal_ac_m = NULL;
        }
        
        if (!empty($tanggal_ac_m)) {
            $tahun_ac = date("Y", strtotime($tanggal_ac_m));
            $hari_ac = date("l", strtotime($tanggal_ac_m));
        } else {
            $tahun_ac = NULL;
            $hari_ac = NULL;
        }
    $no_perkara = $_POST['NO_PERKARA'] ?? NULL;
    $tahun_perkara = $_POST['TAHUN_PERKARA'] ?? NULL;
    $tahun_perkara = empty($tahun_perkara) ? NULL : $tahun_perkara;
    $tanggal_putus = !empty($_POST['TANGGAL_PUTUS']) ? $_POST['TANGGAL_PUTUS'] : NULL;
    $nama_p = $_POST['NAMA_P'] ?? NULL;
    $umur_p = $_POST['UMUR_P'] ?? NULL;
    $binti_p = $_POST['BINTI_P'] ?? NULL;
    $pekerjaan_p = $_POST['PEKERJAAN_P'] ?? NULL;
    $alamat_p = $_POST['ALAMAT_P'] ?? NULL;
    $alamat_p_rt_rw = $_POST['ALAMAT_P_RT_RW'] ?? NULL;
    $alamat_p_kelurahan = $_POST['ALAMAT_P_KELURAHAN'] ?? NULL;
    $kecamatan_p = $_POST['KECAMATAN_P'] ?? NULL;
    $kabupaten_kota_p = $_POST['KABUPATEN_KOTA_P'] ?? NULL;
    $nama_t = $_POST['NAMA_T'] ?? NULL;
    $umur_t = $_POST['UMUR_T'] ?? NULL;
    $bin_t = $_POST['BIN_T'] ?? NULL;
    $pekerjaan_t = $_POST['PEKERJAAN_T'] ?? NULL;
    $alamat_t = $_POST['ALAMAT_T'] ?? NULL;
    $alamat_t_rt_rw = $_POST['ALAMAT_T_RT_RW'] ?? NULL;
    $alamat_t_kelurahan = $_POST['ALAMAT_T_KELURAHAN'] ?? NULL;
    $kecamatan_t = $_POST['KECAMATAN_T'] ?? NULL;
    $kabupaten_kota_t = $_POST['KABUPATEN_KOTA_T'] ?? NULL;
    $kua_kecamatan = $_POST['KUA_KECAMATAN'] ?? NULL;
    $kabupaten_kota_kua = $_POST['KABUPATEN_KOTA_KUA'] ?? NULL;
    $tanggal_nikah = !empty($_POST['TANGGAL_NIKAH']) ? $_POST['TANGGAL_NIKAH'] : NULL;
    $no_aktanikah = $_POST['NO_AKTANIKAH'] ?? NULL;

    // Cek apakah file diunggah
    $file_name = NULL;
    $file_type = NULL;
    $file_data = NULL;

    if (isset($_FILES["FILE"]) && $_FILES["FILE"]["error"] == 0) {
        $file_name = $_FILES["FILE"]["name"];
        $file_type = $_FILES["FILE"]["type"];
        $file_tmp = $_FILES["FILE"]["tmp_name"];
        $file_data = file_get_contents($file_tmp);
    }

    // Query simpan ke database
    $stmt = $conn->prepare("INSERT INTO data_ac 
    (NO_AC, TAHUN_AC, HARI_AC, TANGGAL_AC_M, NO_PERKARA, TAHUN_PERKARA, TANGGAL_PUTUS, 
    NAMA_P, UMUR_P, BINTI_P, PEKERJAAN_P, ALAMAT_P, ALAMAT_P_RT_RW, ALAMAT_P_KELURAHAN, KECAMATAN_P, KABUPATEN_KOTA_P, 
    NAMA_T, UMUR_T, BIN_T, PEKERJAAN_T, ALAMAT_T, ALAMAT_T_RT_RW, ALAMAT_T_KELURAHAN, KECAMATAN_T, KABUPATEN_KOTA_T, 
    KUA_KECAMATAN, KABUPATEN_KOTA_KUA, TANGGAL_NIKAH, NO_AKTANIKAH, FILE_NAME, FILE_TYPE, FILE_DATA) 
    VALUES (?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Tipe data harus sama jumlahnya dengan jumlah tanda tanya (?)
$stmt->bind_param("isssiississssssssisssssssssssssb", 
    $no_ac, $tahun_ac, $hari_ac, $tanggal_ac_m, $no_perkara, $tahun_perkara, $tanggal_putus,
    $nama_p, $umur_p, $binti_p, $pekerjaan_p, $alamat_p, $alamat_p_rt_rw, $alamat_p_kelurahan, $kecamatan_p, $kabupaten_kota_p,
    $nama_t, $umur_t, $bin_t, $pekerjaan_t, $alamat_t, $alamat_t_rt_rw, $alamat_t_kelurahan, $kecamatan_t, $kabupaten_kota_t,
    $kua_kecamatan, $kabupaten_kota_kua, $tanggal_nikah, $no_aktanikah, $file_name, $file_type, $file_data
);


// Kirim data file dengan send_long_data()
    if ($file_data !== NULL) {
        $stmt->send_long_data(31, $file_data);
    }


    
    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>window.location.href='upload.php'</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
