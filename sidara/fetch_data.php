<?php
session_start();
include 'koneksi.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search = $conn->real_escape_string($search);
$search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$offset = ($page - 1) * $limit;

if ($limit <= 0) $limit = 100;
if ($offset < 0) $offset = 0;

// Query untuk menghitung total data setelah filter diterapkan
$countQuery = "SELECT COUNT(*) as total FROM data_ac WHERE 1";
$countParams = [];
$countParamTypes = "";

// Gunakan kondisi filter yang sama seperti query utama
if (!empty($search)) {
    if (ctype_digit($search)) {
        $countQuery .= " AND (NO_AC = ? OR NO_AC LIKE CONCAT(?, '%') OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? OR TANGGAL_AC_M LIKE ? OR FILE_NAME LIKE ?)";
        array_unshift($countParams, "$search", "$search", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%");
    } else {
        $countQuery .= " AND (NO_AC LIKE ? OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? OR TANGGAL_AC_M LIKE ? OR FILE_NAME LIKE ?)";
        $countParams = array_merge(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
    }
    $countParamTypes .= str_repeat("s", count($countParams));
}

// Tambahkan filter bulan dan tahun
if (!empty($bulan)) {
    $countQuery .= " AND MONTH(STR_TO_DATE(TANGGAL_AC_M, '%Y-%m-%d')) = ?";
    $countParams[] = $bulan;
    $countParamTypes .= "i";
}

if (!empty($tahun)) {
    $countQuery .= " AND YEAR(STR_TO_DATE(TANGGAL_AC_M, '%Y-%m-%d')) = ?";
    $countParams[] = $tahun;
    $countParamTypes .= "i";
}

// Prepare statement untuk menghitung total data
$countStmt = $conn->prepare($countQuery);
if ($countParamTypes) {
    $countStmt->bind_param($countParamTypes, ...$countParams);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$countStmt->close();

// Hitung total halaman
$totalPages = ceil($totalRows / $limit);

// Jika data kosong, tampilkan pesan dan hentikan eksekusi
if ($totalRows == 0) {
    echo "<tr><td colspan='7' style='text-align: center; color: red;'>Data tidak ditemukan</td></tr>";
    return;
}

// Query utama untuk mengambil data dengan filter dan pagination
$query = "SELECT NO_AC, ID, TANGGAL_AC_M, NO_PERKARA, NAMA_P, NAMA_T, FILE_NAME FROM data_ac WHERE 1";
$paramTypes = "";
$params = [];

// Tambahkan filter pencarian
if (!empty($search)) {
    if (ctype_digit($search)) {
        $query .= " AND (NO_AC = ? OR NO_AC LIKE CONCAT(?, '%') OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? OR TANGGAL_AC_M LIKE ? OR FILE_NAME LIKE ?)";
        array_unshift($params, "$search", "$search", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%");
    } else {
        $query .= " AND (NO_AC LIKE ? OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? OR TANGGAL_AC_M LIKE ? OR FILE_NAME LIKE ?)";
        $params = array_merge(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
    }
    $paramTypes .= str_repeat("s", count($params));
}

// Filter berdasarkan bulan dan tahun
if (!empty($bulan)) {
    $query .= " AND MONTH(STR_TO_DATE(TANGGAL_AC_M, '%Y-%m-%d')) = ?";
    $params[] = $bulan;
    $paramTypes .= "i";
}

if (!empty($tahun)) {
    $query .= " AND YEAR(STR_TO_DATE(TANGGAL_AC_M, '%Y-%m-%d')) = ?";
    $params[] = $tahun;
    $paramTypes .= "i";
}

$query .= " ORDER BY ID ASC";
// $query .= " ORDER BY 
//             YEAR(STR_TO_DATE(TANGGAL_AC_M, '%Y-%m-%d')) ASC, 
//             CAST(NO_AC AS UNSIGNED) ASC
//             LIMIT ? OFFSET ?";
$query .= " LIMIT ? OFFSET ?"; // dihapus jika menggunakan query tahun
$params[] = $limit;
$params[] = $offset;
$paramTypes .= "ii";

// Eksekusi query
$stmt = $conn->prepare($query);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
// Fetch and display data
// $no = 1; tidak perlu (jika menggunakan id)
while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['ID']; ?></td>
        <td><?php echo $row['NO_AC'] ? htmlspecialchars($row['NO_AC']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
        <td>
            <?php 
            if ($row['TANGGAL_AC_M']) {
                $date = new DateTime($row['TANGGAL_AC_M']);
                $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                echo $formatter->format($date);
            } else {
                echo '<span class="text-red">Tidak tersedia</span>';
            }
            ?>
        </td>
        <td><?php echo $row['NO_PERKARA'] ? htmlspecialchars($row['NO_PERKARA']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
        <td><?php echo $row['NAMA_P'] ? htmlspecialchars($row['NAMA_P']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
        <td><?php echo $row['NAMA_T'] ? htmlspecialchars($row['NAMA_T']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
        <td style="text-align: center; vertical-align: middle;">
            <div style="display: flex; justify-content: center; gap: 2px;">
                <button class="detail-button" onclick="fetchDetail(<?php echo $row['ID']; ?>)">
                    <img src="assets/personal-data.png" title="Detail" alt="Lihat Detail" width="20" height="20">D
                </button>
                <?php if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === true) : ?>
            <button class="edit-button" onclick="editData(<?php echo $row['ID']; ?>)">
                <img src="assets/edit.png" title="Edit" alt="Edit" width="20" height="20">E
            </button>
            <button class="delete-button" onclick="deleteData(<?php echo $row['ID']; ?>)">
                <img src="assets/delete.png" title="Delete" alt="Hapus" width="20" height="20">H
            </button>
            <button class="print-button" onclick="cetakData(<?php echo $row['ID']; ?>)">
                <img src="assets/printer.png" title="Cetak" alt="Cetak" width="20" height="20">C
            </button>
        <?php endif; ?>
            </div>
        </td>
        <td style="text-align: center; vertical-align: middle;">
        <?php if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === true && $row['FILE_NAME']) { ?>
        <form method="post" action="set_session.php" style="display:inline;" target="_blank">
            <input type="hidden" name="file_id" value="<?php echo $row['ID']; ?>">
            <button type="submit" class="green-button">
                <img src="assets/view.png" title="Lihat" alt="Lihat" width="20" height="20">L
            </button>
        </form>
    <?php } else { ?>
        <button class="grey-button"><img src="assets/no-view.png" alt="Lihat" width="20" height="20"></button>
    <?php } ?>
        </td>
    </tr>
<?php }

$stmt->close();
$conn->close();
?>
