<?php
include 'koneksi.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$nama_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret",
    "04" => "April", "05" => "Mei", "06" => "Juni",
    "07" => "Juli", "08" => "Agustus", "09" => "September",
    "10" => "Oktober", "11" => "November", "12" => "Desember"
];
$query = "SELECT * FROM data_ac WHERE 1";


if (!empty($bulan) && !empty($tahun)) {
    $query .= " AND TANGGAL_AC_M LIKE '$tahun-$bulan%'";
    $filter_info = "Menampilkan data untuk bulan " . ($nama_bulan[$bulan] ?? "Tidak Diketahui") . " tahun $tahun";
} elseif (!empty($tahun)) {
    $query .= " AND TANGGAL_AC_M LIKE '$tahun-%'";
    $filter_info = "Menampilkan data untuk tahun $tahun";
} else {
    $filter_info = "Menampilkan semua data";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data AC</title>
    <style>
        body {font-family: "Times New Roman", Times, serif; font-size: 14px;}
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }

        .header-container {
            display: flex;
            align-items: center;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 2;
        }

        .header-container img {
        width: 50px; 
        margin-right: 15px;
        filter: grayscale(100%); 
        }

        .content {
        margin-top: 60px;
        text-align: center;
        }
        .space{
            margin-top: -20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <img src="assets/logo.png" alt="Logo"> <!-- Ganti dengan path gambar -->
        <div>
            <h3>PENGADILAN AGAMA MALANG</h3>
            <p class="space">Jl. Raden Panji Suroso No.1, Malang 65126</p>
        </div>
    </div>
    <div class="content">
        <h3>DATA AKTA CERAI</h3>
        <p class="space"> <?php if (!empty($bulan) && !empty($tahun)) { ?>
            Bulan <strong><?= $nama_bulan[$bulan] ?? ''; ?></strong> 
            Tahun <strong><?= $tahun; ?></strong>
        <?php } elseif (!empty($tahun)) { ?>
            Tahun <strong><?= $tahun; ?></strong>
        <?php } else { ?>
            Menampilkan semua data
        <?php } ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No. AC</th>
                <th>Tanggal AC</th>
                <th>No. Perkara</th>
                <th>Nama Penggugat</th>
                <th>Nama Tergugat</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>
                    <?php 
                        $ac = "AC"; 
                        $pa = "PA.MLG";

                        $no_ac = isset($row['NO_AC']) && !empty($row['NO_AC']) ? str_pad($row['NO_AC'], 4, '0', STR_PAD_LEFT) : NULL;
                        $tahun_ac = isset($row['TANGGAL_AC_M']) && !empty($row['TANGGAL_AC_M']) ? date('Y', strtotime($row['TANGGAL_AC_M'])) : NULL;
                        if ($no_ac === NULL && $tahun_ac === NULL) {
                            echo "-";
                        } else {
                            echo ($no_ac ?? "0000") . "/{$ac}/" . ($tahun_ac ?? "0000") . "/{$pa}";
                        }
                    ?>
                </td>

                <td><?= isset($row['TANGGAL_AC_M']) && !empty($row['TANGGAL_AC_M']) ? date("d-m-Y", strtotime($row['TANGGAL_AC_M'])) : "-"; ?>
                </td>
                <td>
                    <?php 
                        $pdt = "Pdt.G"; 
                        $pa = "PA.MLG"; 

                        $no_perkara = isset($row['NO_PERKARA']) && !empty($row['NO_PERKARA']) ? str_pad($row['NO_PERKARA'], 4, '0', STR_PAD_LEFT) : NULL;
                        $tahun_perkara = isset($row['TAHUN_PERKARA']) && !empty($row['TAHUN_PERKARA']) ? $row['TAHUN_PERKARA'] : NULL ;
                        if ($no_perkara === NULL && $tahun_perkara === NULL) {
                            echo "-";
                        } else {
                            echo ($no_perkara ?? "0000") . "/{$pdt}/" . ($tahun_perkara ?? "0000") . "/{$pa}";
                        }
                    ?>

                </td>

                <td><?= isset($row['NAMA_P']) && !empty($row['NAMA_P']) ? $row['NAMA_P'] : "-"; ?></td>
                <td><?= isset($row['NAMA_T']) && !empty($row['NAMA_T']) ? $row['NAMA_T'] : "-"; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
