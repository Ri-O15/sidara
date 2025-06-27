<?php
session_start();
include 'koneksi.php';

// Pastikan ID tersedia
if (!isset($_GET['id'])) {
    die("Data tidak valid.");
}

$id = intval($_GET['id']);
$query = $conn->prepare("SELECT * FROM data_ac WHERE ID = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data AC</title>
    <style>
         @media print {
        body * {
            visibility: hidden;
        }
        table, table * {
            visibility: visible;
        }
        table {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
        body { font-family: "Times New Roman", Times, serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 10px; text-align: left; }
    </style>
</head>
<body onload="window.print();">
    <table>
        <tr><th>Nomor Akta Cerai</th>
            <td>
                <?php 
                    $no_ac = !empty($data['NO_AC']) ? str_pad($data['NO_AC'], 4, '0', STR_PAD_LEFT) : '0000';
                    $ac = "AC"; // Ganti sesuai kebutuhan
                    $tahun_ac = !empty($data['TANGGAL_AC_M']) ? date('Y', strtotime($data['TANGGAL_AC_M'])) : '0000';
                    $pa = "PA.MLG"; // Ganti sesuai kebutuhan

                    $no_ac_display = ($no_ac === '0000' && $tahun_ac === '0000') ? "-" : "{$no_ac}/{$ac}/{$tahun_ac}/{$pa}";

                    echo htmlspecialchars($no_ac_display);
                ?>
            </td>
        </tr>
        <tr>
            <th>Tanggal Akta Cerai</th>
            <td>
                <?php 
                if (!empty($data['TANGGAL_AC_M'])) {
                    $date = new DateTime($data['TANGGAL_AC_M']);
                    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    echo htmlspecialchars($formatter->format($date));
                } else {
                    echo '-';
                }
                ?>
            </td>
        </tr>

        <tr>
            <th>No Perkara</th>
            <td>
                <?php 
                    $no_perkara = !empty($data['NO_PERKARA']) ? str_pad($data['NO_PERKARA'], 4, '0', STR_PAD_LEFT) : '0000';
                    $tahun_perkara = !empty($data['TANGGAL_AC_M']) ? date('Y', strtotime($data['TANGGAL_AC_M'])) : '0000';
                    $pa = "PA.MLG"; // Ganti jika ada kode lain untuk pengadilan
                    $pdt = "Pdt.G";
                    $no_perkara_display = ($no_perkara === '0000' && $tahun_perkara === '0000') ? "-" : "{$no_perkara}/{$pdt}/{$tahun_perkara}/{$pa}";

                    echo htmlspecialchars($no_perkara_display);
                ?>
            </td>
        </tr>
        <tr><th>Tahun Perkara</th><td><?php echo !empty($data['TAHUN_PERKARA']) ? htmlspecialchars($data['TAHUN_PERKARA']) : '-'; ?></td></tr>
        <tr>
        <th>Tanggal Putus</th>
            <td>
                <?php 
                if (!empty($data['TANGGAL_PUTUS'])) {
                    $date = new DateTime($data['TANGGAL_PUTUS']);
                    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    echo htmlspecialchars($formatter->format($date));
                } else {
                    echo '-';
                }
                ?>
            </td>
        </tr>

        <tr><th>Nama Penggugat</th><td><?php echo !empty($data['NAMA_P']) ? htmlspecialchars($data['NAMA_P']) : '-'; ?></td></tr>
        <tr><th>Umur Penggugat</th><td><?php echo !empty($data['UMUR_P']) ? htmlspecialchars($data['UMUR_P']) : '-'; ?></td></tr>
        <tr><th>Binti Penggugat</th><td><?php echo !empty($data['BINTI_P']) ? htmlspecialchars($data['BINTI_P']) : '-'; ?></td></tr>
        <tr><th>Pekerjaan Penggugat</th><td><?php echo !empty($data['PEKERJAAN_P']) ? htmlspecialchars($data['PEKERJAAN_P']) : '-'; ?></td></tr>
        <tr><th>Alamat Penggugat</th><td> <?php 
                $alamat_penggugat = trim(
                    ($data['ALAMAT_P'] ?? '') . ' ' .
                    ($data['ALAMAT_P_RT_RW'] ?? '') . ' ' .
                    ($data['ALAMAT_P_KELURAHAN'] ?? '') . ' ' .
                    ($data['ALAMAT_P_KELURAHAN'] ?? '') . ' ' .
                    ($data['KECAMATAN_P'] ?? '') . ' ' .
                    ($data['KABUPATEN_KOTA_P'] ?? '')
                );
                echo htmlspecialchars($alamat_penggugat ?: '-');
            ?></td></tr>
        <tr><th>Nama Tergugat</th><td><?php echo !empty($data['NAMA_T']) ? htmlspecialchars($data['NAMA_T']) : '-'; ?></td></tr>
        <tr><th>Umur Tergugat</th><td><?php echo !empty($data['UMUR_T']) ? htmlspecialchars($data['UMUR_T']) : '-'; ?></td></tr>
        <tr><th>Bin Tergugat</th><td><?php echo !empty($data['BIN_T']) ? htmlspecialchars($data['BIN_T']) : '-'; ?></td></tr>
        <tr><th>Pekerjaan Tergugat</th><td><?php echo !empty($data['PEKERJAAN_T']) ? htmlspecialchars($data['PEKERJAAN_T']) : '-'; ?></td></tr>
        <tr><th>Alamat Tergugat</th><td> <?php 
                $alamat_Tergugat = trim(
                    ($data['ALAMAT_T'] ?? '') . ' ' .
                    ($data['ALAMAT_T_RT_RW'] ?? '') . ' ' .
                    ($data['ALAMAT_T_KELURAHAN'] ?? '') . ' ' .
                    ($data['ALAMAT_T_KELURAHAN'] ?? '') . ' ' .
                    ($data['KECAMATAN_T'] ?? '') . ' ' .
                    ($data['KABUPATEN_KOTA_T'] ?? '')
                );
                echo htmlspecialchars($alamat_Tergugat ?: '-');
            ?></td></tr>
        <tr><th>KUA Kecamatan</th><td><?php echo !empty($data['KUA_KECAMATAN']) ? htmlspecialchars($data['KUA_KECAMATAN']) : '-'; ?></td></tr>
        <tr><th>Kabupaten Kota KUA</th><td><?php echo !empty($data['KABUPATEN_KOTA_KUA']) ? htmlspecialchars($data['KABUPATEN_KOTA_KUA']) : '-'; ?></td></tr>
        <tr>
        <th>Tanggal Nikah</th>
            <td>
                <?php 
                if (!empty($data['TANGGAL_NIKAH'])) {
                    $date = new DateTime($data['TANGGAL_NIKAH']);
                    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    echo htmlspecialchars($formatter->format($date));
                } else {
                    echo '-';
                }
                ?>
            </td>
        </tr>

        <tr><th>Nomor Akta Nikah</th><td><?php echo !empty($data['NO_AKTANIKAH']) ? htmlspecialchars($data['NO_AKTANIKAH']) : '-'; ?></td></tr>
    </table>
</body>
</html>
