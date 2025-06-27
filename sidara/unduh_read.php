<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT FILE_NAME, FILE_DATA, FILE_TYPE FROM data_ac WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_name, $file_data, $file_type);
        $stmt->fetch();

        // Set header untuk unduhan file
        header("Content-Type: " . $file_type);
        header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
        header("Content-Length: " . strlen($file_data));

        echo $file_data;
        exit();
    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "Parameter ID tidak ditemukan.";
}
?>
