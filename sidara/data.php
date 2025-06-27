<?php
session_start();
include 'koneksi.php';
include 'logoTitle.php';
include 'sesi.php';

if (!isset($_SESSION['from_home']) || $_SESSION['from_home'] !== true) {
    header("Location: home.php");
    exit();
}

// Set jumlah data per halaman
$limit = 100;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : (isset($_SESSION['page']) ? $_SESSION['page'] : 1);
$offset = ($page - 1) * $limit;

// Simpan pencarian dan filter dalam sesi
if (isset($_GET['search'])) {
    $_SESSION['search'] = trim($_GET['search']);
}
if (isset($_GET['filter'])) {
    $_SESSION['filter'] = $_GET['filter'];
}

// Ambil nilai dari sesi jika tidak ada input baru
$search = isset($_SESSION['search']) ? $_SESSION['search'] : '';
$filter = isset($_SESSION['filter']) ? $_SESSION['filter'] : 'all';

// Query utama
$query = "SELECT ID, NO_AC, TANGGAL_AC_M, NO_PERKARA, NAMA_P, NAMA_T, FILE_NAME FROM data_ac WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (NO_AC LIKE ? OR TANGGAL_AC_M LIKE ? OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? )";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $types .= "sssss";
}

// Filter data kosong
if ($filter === 'empty') {
    $query .= " AND (COALESCE(NO_AC, '') = '' OR COALESCE(TANGGAL_AC_M, '') = '' OR 
                     COALESCE(NO_PERKARA, '') = '' OR COALESCE(NAMA_P, '') = '' OR 
                     COALESCE(NAMA_T, '') = '')";
}

// Pastikan urutan data konsisten
$query .= " ORDER BY ID ASC";

// Hitung total data setelah filter diterapkan
$total_query = "SELECT COUNT(*) as total FROM data_ac WHERE 1";
$total_params = [];
$total_types = "";

if (!empty($search)) {
    $total_query .= " AND (NO_AC LIKE ? OR TANGGAL_AC_M LIKE ? OR NO_PERKARA LIKE ? OR NAMA_P LIKE ? OR NAMA_T LIKE ? )";
    $total_params = array_merge($total_params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $total_types .= "sssss";
}
if ($filter === 'empty') {
    $total_query .= " AND (COALESCE(NO_AC, '') = '' OR COALESCE(TANGGAL_AC_M, '') = '' OR 
                           COALESCE(NO_PERKARA, '') = '' OR COALESCE(NAMA_P, '') = '' OR 
                           COALESCE(NAMA_T, '') = '')";
}

// Jalankan query total data dengan prepared statement
$stmt_total = $conn->prepare($total_query);
if (!empty($total_params)) {
    $stmt_total->bind_param($total_types, ...$total_params);
}
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);
$stmt_total->close();

// Tambahkan LIMIT dan OFFSET
$query .= " LIMIT ? OFFSET ?";
$params = array_merge($params, [$limit, $offset]);
$types .= "ii";

// Jalankan query utama dengan prepared statement
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Simpan halaman terakhir ke sesi
$_SESSION['page'] = $page;
?>


<!DOCTYPE html>
<html>
<head>
    <title>Data AC</title>
            <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="css/data.css">
</head>
<body>
<div style="display: flex; justify-content: center; align-items: center; position: relative; padding: 1% 2%;">
<h2 style="font-weight: bold; margin: 0; text-align: center; flex-grow: 1;">DAFTAR AKTA CERAI</h2>
<button onclick="window.location.href='home.php'" class="btn btn-outline-secondary" style="position: absolute; right: 2%;">Back</button>
</div>
        <div class="header-container">
        <div class="bg-container"></div>
            <div class="header-title">Pengadilan Agama Kota Malang</div>
        </div>

        <div class="container">
        <div class="left-container">
            <label for="limitSelect">Short:</label>
            <select id="limitSelect" onchange="applyFilter(1)">
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

           <!-- Tombol untuk membuka modal cetak -->
           <?php if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === true) : ?>
    <button type="button" class="printer-button" data-bs-toggle="modal" data-bs-target="#printModal">
        <img src="assets/printer_full.png" title="Cetak Data" alt="Cetak Data" width="20" height="20">
    </button>
<?php endif; ?>

<label for="bulanSelect">Bulan:</label>
<select id="bulanSelect" onchange="applyFilter(1)">
    <option value="">Semua</option>
    <option value="01">Januari</option>
    <option value="02">Februari</option>
    <option value="03">Maret</option>
    <option value="04">April</option>
    <option value="05">Mei</option>
    <option value="06">Juni</option>
    <option value="07">Juli</option>
    <option value="08">Agustus</option>
    <option value="09">September</option>
    <option value="10">Oktober</option>
    <option value="11">November</option>
    <option value="12">Desember</option>
</select>

<label for="tahunSelect">Tahun:</label>
<select id="tahunSelect" onchange="applyFilter(1)">
    <option value="">Semua</option>
    <script>
        for (let i = new Date().getFullYear(); i >= 1975; i--) {
            document.write(`<option value="${i}">${i}</option>`);
        }
    </script>
</select>

        </div>
        

        <div class="right-container">
            <label for="searchInput">Cari: </label>
            <input type="text" id="searchInput" onkeyup="applyFilter()" placeholder="Cari data...">
            <div id="filterSelect" onchange="applyFilter()">
                <!-- <option value="all">Semua Data</option>
                <option value="empty">Hanya Data Kosong</option> -->
            </div>
        </div>
    </div>

<!-- Elemen tersembunyi untuk menyimpan data cetakan -->
<iframe id="printFrame" style="display:none;"></iframe>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="3%">No.</th>
                        <th width="5%">No AC</th>
                        <th width="12%">Tanggal AC</th>
                        <th width="7%">No Perkara</th>
                        <th width="30%">Nama Penggugat</th>
                        <th>Nama Tergugat</th>
                        <th width="14%">Aksi</th>
                        <th width="3%">File</th>
                    </tr>
                </thead>
                <tbody id="dataBody">
                    <?php
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
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Cetak -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #009578;">
                <h5 class="modal-title" id="printModalLabel">Cetak Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="printBulan">Pilih Bulan:</label>
                <select id="printBulan" class="form-select">
                    <option value="">Semua</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>

                <label for="printTahun" class="mt-2">Pilih Tahun:</label>
                <select id="printTahun" class="form-select">
                    <option value="" disabled selected>Pilih Tahun</option>
                    <?php for ($i = date("Y"); $i >= 1975; $i--) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printData()">Cetak</button>
            </div>
        </div>
    </div>
</div>

        <!-- Modal Detail -->
        <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #009578;">
                        <h5 class="modal-title" id="dataModalLabel">Detail Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-stripped table-bordered text-start">
                            <tbody id="modalBody">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal Edit Data -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #009578;">
                <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editId" name="ID">
                    <table class="table table-bordered text-start">
                        <tbody>
                            <tr>
                                <th><label for="editNoAc" class="form-label">Nomor Akta Cerai</label></th>
                                <td><input type="number" class="form-control" id="editNoAc" name="NO_AC" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)"></td>
                            </tr>
                            <tr>
                                <th><label for="editTanggalAc" class="form-label">Tanggal Akta Cerai</label></th>
                                <td><input type="date" class="form-control" id="editTanggalAc" name="TANGGAL_AC_M"></td>
                            </tr>
                            <tr>
                                <th><label for="editNoPerkara" class="form-label">Nomor Perkara</label></th>
                                <td><input type="number" class="form-control" id="editNoPerkara" name="NO_PERKARA" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)"></td>
                            </tr>
                            <tr>
                                <th><label for="editTahunPerkara" class="form-label">Tahun Perkara</label></th>
                                <td><input type="number" class="form-control" id="editTahunPerkara" name="TAHUN_PERKARA" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)"></td>
                            </tr>
                            <tr>
                                <th><label for="editTanggalPutus" class="form-label">Tanggal Putus</label></th>
                                <td><input type="date" class="form-control" id="editTanggalPutus" name="TANGGAL_PUTUS"></td>
                            </tr>
                            <tr>
                                <th><label for="editNamaP" class="form-label">Nama Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editNamaP" name="NAMA_P"></td>
                            </tr>
                             <tr>
                                <th><label for="editUmurP" class="form-label">Umur Penggugat</label></th>
                                <td><input type="number" class="form-control" id="editUmurP" name="UMUR_P" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)"></td>
                            </tr>
                            <tr>
                                <th><label for="editBintiP" class="form-label">Binti Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editBintiP" name="BINTI_P"></td>
                            </tr>
                            <tr>
                                <th><label for="editPekerjaanP" class="form-label">Pekerjaan Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editPekerjaanP" name="PEKERJAAN_P"></td>
                            </tr>
                            <tr>
                                <th><label for="editAlamatP" class="form-label">Alamat Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editAlamatP" name="ALAMAT_P"></td>
                            </tr> 
                            <tr>
                                <th><label for="editAlamatPRtRw" class="form-label">Alamat Penggugat RT TW</label></th>
                                <td><input type="text" class="form-control" id="editAlamatPRtRw" name="ALAMAT_P_RT_RW"></td>
                            </tr>
                            <tr>
                                <th><label for="editAlamatPKelurahan" class="form-label">Alamat Penggugat Kelurahan</label></th>
                                <td><input type="text" class="form-control" id="editAlamatPKelurahan" name="ALAMAT_P_KELURAHAN"></td>
                            </tr>
                            <tr>
                                <th><label for="editKecamatanP" class="form-label">Kecamatan Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editKecamatanP" name="KECAMATAN_P"></td>
                            </tr>             
                            <tr>
                                <th><label for="editKabupatenKotaP" class="form-label">Kabupaten Kota Penggugat</label></th>
                                <td><input type="text" class="form-control" id="editKabupatenKotaP" name="KABUPATEN_KOTA_P"></td>
                            </tr>
                            <tr>
                                <th><label for="editNamaT" class="form-label">Nama Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editNamaT" name="NAMA_T"></td>
                            </tr>
                            <tr>
                                <th><label for="editUmurT" class="form-label">Umur Tergugat</label></th>
                                <td><input type="number" class="form-control" id="editUmurT" name="UMUR_T" min="1" onkeydown="blockInvalidInput(event)" oninput="fixInvalidInput(this)"></td>
                            </tr>
                            <tr>
                                <th><label for="editBinT" class="form-label">Bin Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editBinT" name="BIN_T"></td>
                            </tr>
                            <tr>
                                <th><label for="editPekerjaanT" class="form-label">Pekerjaan Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editPekerjaanT" name="PEKERJAAN_T"></td>
                            </tr>
                            <tr>
                                <th><label for="editAlamatT" class="form-label">Alamat Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editAlamatT" name="ALAMAT_T"></td>
                            </tr> 
                            <tr>
                                <th><label for="editAlamatTRtRw" class="form-label">Alamat Tergugat RT TW</label></th>
                                <td><input type="text" class="form-control" id="editAlamatTRtRw" name="ALAMAT_T_RT_RW"></td>
                            </tr>
                            <tr>
                                <th><label for="editAlamatTKelurahan" class="form-label">Alamat Tergugat Kelurahan</label></th>
                                <td><input type="text" class="form-control" id="editAlamatTKelurahan" name="ALAMAT_T_KELURAHAN"></td>
                            </tr>
                            <tr>
                                <th><label for="editKecamatanT" class="form-label">Kecamatan Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editKecamatanT" name="KECAMATAN_T"></td>
                            </tr>             
                            <tr>
                                <th><label for="editKabupatenKotaT" class="form-label">Kabupaten Kota Tergugat</label></th>
                                <td><input type="text" class="form-control" id="editKabupatenKotaT" name="KABUPATEN_KOTA_T"></td>
                            </tr>
                            <tr>
                                <th><label for="editKUAKecamatan" class="form-label">KUA Kecamatan</label></th>
                                <td><input type="text" class="form-control" id="editKUAKecamatan" name="KUA_KECAMATAN"></td>
                            </tr>
                            <tr>
                                <th><label for="editKabupatenKotaKUA" class="form-label">Kabupaten Kota KUA</label></th>
                                <td><input type="text" class="form-control" id="editKabupatenKotaKUA" name="KABUPATEN_KOTA_KUA"></td>
                            </tr>
                            <tr>
                                <th><label for="editTanggalNikah" class="form-label">Tanggal Nikah</label></th>
                                <td><input type="date" class="form-control" id="editTanggalNikah" name="TANGGAL_NIKAH"></td>
                            </tr> 
                            <tr>
                                <th><label for="editNoAktaNikah" class="form-label">Nomor Akta Nikah</label></th>
                                <td><input type="text" class="form-control" id="editNoAktaNikah" name="NO_AKTANIKAH"></td>
                            </tr>
                            <tr>
                                <th><label for="editFile" class="form-label">Upload File (Opsional)</label></th>
                                <td><input type="file" class="form-control" id="editFile" name="FILE" accept=".pdf"></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success mt-2" onclick="updateData()">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo $page - 1; ?>">Prev</a>
            <?php } ?>
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) { ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"> <?php echo $i; ?> </a>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php } ?>
        </div>
        
        <script src="js/data.js"></script>
    </body>
</html>
<?php
$conn->close();
?>
