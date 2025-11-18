<?php
require 'koneksi.php';
$conn = new mysqli($host, $username, $password, $dbname);

$id = $_POST['id'];
$status = $_POST['status'];

// Ambil status lama
$cek = $conn->query("SELECT status FROM surat WHERE id = $id");
$row = $cek->fetch_assoc();

if ($row && $row['status'] != $status) {
    $tanggalSekarang = date('Y-m-d');
    $stmt = $conn->prepare("UPDATE surat SET status = ?, tanggal_status_update = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $tanggalSekarang, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'tanggal' => date('d/m/Y', strtotime($tanggalSekarang))]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    // Tidak ada perubahan status
    echo json_encode(['success' => false]);
}
?>
