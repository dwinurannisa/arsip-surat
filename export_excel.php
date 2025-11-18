<?php
require 'koneksi.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Validasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_list'])) {
    $id_list = explode(',', $_POST['id_list']);

    // Hindari SQL injection dengan prepared statement
    $placeholders = implode(',', array_fill(0, count($id_list), '?'));

    $sql = "SELECT * FROM surat WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Query Error: " . $conn->error);
    }

    // Binding parameter (asumsi id adalah integer)
    $types = str_repeat('i', count($id_list));
    $stmt->bind_param($types, ...$id_list);
    $stmt->execute();
    $result = $stmt->get_result();

    // Header untuk file Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=arsip_surat_terfilter.xls");

    // Header kolom
    echo "ID\tNO. AGENDA\tSURAT DARI\tNO. SURAT\tTANGGAL SURAT\tTANGGAL DITERIMA\tPERIHAL\tBIDANG\tSTATUS\tTANGGAL STATUS UPDATE\n";

    // Isi baris
    while ($row = $result->fetch_assoc()) {
        echo "{$row['id']}\t{$row['no_agenda']}\t{$row['surat_dari']}\t{$row['no_surat']}\t{$row['tanggal_surat']}\t{$row['tanggal_diterima']}\t{$row['perihal']}\t{$row['bidang']}\t{$row['status']}\t{$row['tanggal_status_update']}\n";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Permintaan tidak valid.";
}
?>
