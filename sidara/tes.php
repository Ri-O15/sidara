<?php
session_start();
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) {
    header("Location: home.php");
    exit();
}
include 'koneksi.php';

$query = "SELECT ID, NO_AC, TANGGAL_AC_M, NO_PERKARA, NAMA_P, NAMA_T, FILE_NAME FROM data_ac";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .table-container {
            height: 80vh;
            width: 95%;
            margin: 0 auto;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #ddd;
            background: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            /* min-width: 900px; Ensures table maintains minimum width */
        }
        th {
            padding: 12px;
            border-right: 1px solid #ccc;
            border-left: 1px solid #ccc;
        }
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            white-space: nowrap;
        }
        thead {
            text-align: center;
            white-space: nowrap;
            background-color: #009578;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
    
        .search-container {
            margin: 10px auto;
            width: 50%;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: left;
        }
        .green-button {
            background-color: green;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .text-red {
            color: red;
            font-weight: bold;
        }
    </style>
    <script>
        function filterAndSearch() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let filter = document.getElementById("filterSelect").value;
            let table = document.getElementById("dataTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName("td");
                let empty = false;
                let found = false;
                
                for (let j = 0; j < td.length - 1; j++) {
                    if (td[j].innerText.includes("tidak tersedia")) {
                        empty = true;
                    }
                    if (td[j].innerText.toLowerCase().includes(input)) {
                        found = true;
                    }
                }
                
                if ((filter === "all" || (filter === "empty" && empty)) && found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</head>
<body>
    <h2>Daftar Data Perkara</h2>
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="filterAndSearch()" placeholder="Cari data...">
        <select id="filterSelect" onchange="filterAndSearch()">
            <option value="all">Semua Data</option>
            <option value="empty">Hanya Data Kosong</option>
        </select>
    </div>
    <div class="table-container">
        <table id="dataTable">
            <thead>
                <tr>
                    <th width="2%">No AC</th>
                    <th width="5%">Tanggal AC</th>
                    <th width="2%">No Perkara</th>
                    <th>Nama Penggugat</th>
                    <th>Nama Tergugat</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <!-- <td><?php echo !empty($row['NO_AC']) ? htmlspecialchars($row['NO_AC']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
                    <td><?php echo (!empty($row['TANGGAL_AC_M']) && strtotime($row['TANGGAL_AC_M'])) ? date('d F Y', strtotime($row['TANGGAL_AC_M'])) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
                    <td><?php echo !empty($row['NO_PERKARA']) ? htmlspecialchars($row['NO_PERKARA']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
                    <td><?php echo !empty($row['NAMA_P']) ? htmlspecialchars($row['NAMA_P']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
                    <td><?php echo !empty($row['NAMA_T']) ? htmlspecialchars($row['NAMA_T']) : '<span class="text-red">Tidak tersedia</span>'; ?></td>
                    <td>
                        <?php if ($row['FILE_NAME']) { ?>
                            <form method="post" action="set_session.php" style="display:inline;" target="_blank">
                                <input type="hidden" name="file_id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="green-button">Lihat</button>
                            </form>
                        <?php } else { ?>
                            Tidak ada file
                        <?php } ?> -->
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="">
</head>
<body>
    
</body>
</html>
tampilkan 50 data setiap pagination, pagination tampil 10 yang bisa di prev dan next