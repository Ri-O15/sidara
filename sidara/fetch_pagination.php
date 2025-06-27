<?php
include 'koneksi.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$bulan = isset($_GET['bulan']) ?? '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Hitung total data setelah filter diterapkan
$total_query = "SELECT COUNT(*) as total FROM data_ac WHERE 1";
if (!empty($search)) {
    $total_query .= " AND (NO_AC LIKE '%$search%' OR TANGGAL_AC_M LIKE '%$search%' OR NO_PERKARA LIKE '%$search%' OR NAMA_P LIKE '%$search%' OR NAMA_T LIKE '%$search%')";
}
if ($filter === 'empty') {
    $total_query .= " AND (NO_AC IS NULL OR NO_AC = '' OR TANGGAL_AC_M IS NULL OR TANGGAL_AC_M = '' OR NO_PERKARA IS NULL OR NO_PERKARA = '' OR 
                            NAMA_P IS NULL OR NAMA_P = '' OR NAMA_T IS NULL OR NAMA_T = '')";
}
if (!empty($bulan) && !empty($tahun)) {
    $total_query .= " AND MONTH(TANGGAL_AC_M) = '$bulan' AND YEAR(TANGGAL_AC_M) = '$tahun'";
}

$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];
$total_pages = ($total_data > 0) ? ceil($total_data / $limit) : 0;

if ($total_pages > 1) {
    echo '<div class="pagination">';
    if ($page > 1) {
        echo '<a href="#" onclick="applyFilter('.($page - 1).'); return false;">Prev</a>';
    }
    for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
        echo '<a href="#" onclick="applyFilter('.$i.'); return false;" class="'.($i == $page ? 'active' : '').'">' . $i . '</a>';
    }
    if ($page < $total_pages) {
        echo '<a href="#" onclick="applyFilter('.($page + 1).'); return false;">Next</a>';
    }
    echo '</div>';
}

$conn->close();
?>
