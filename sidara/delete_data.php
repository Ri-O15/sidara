<?php
require 'koneksi.php'; // Pastikan koneksi ke database tersedia

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM data_ac WHERE ID = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menghapus data."]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "ID tidak valid."]);
    }
}
?>
