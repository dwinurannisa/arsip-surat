<?php
require 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$upload_berhasil = false;
$inserted = 0;
$skipped = 0;

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    if (in_array($file_ext, ['xls', 'xlsx'])) {
        $spreadsheet = IOFactory::load($file_tmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }

        $conn->query("SET NAMES utf8mb4");

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $no_agenda = $conn->real_escape_string($row[0]);
            $surat_dari = $conn->real_escape_string($row[1]);
            $no_surat = $conn->real_escape_string($row[2]);
            $tanggal_surat = date('Y-m-d', strtotime($row[3]));
            $tanggal_diterima = date('Y-m-d', strtotime($row[4]));
            $perihal = $conn->real_escape_string($row[5]);

            // Cek duplikat
            $cek = $conn->query("SELECT id FROM surat WHERE no_agenda = '$no_agenda'");
            if ($cek->num_rows == 0) {
                $sql = "INSERT INTO surat (no_agenda, surat_dari, no_surat, tanggal_surat, tanggal_diterima, perihal)
                        VALUES ('$no_agenda', '$surat_dari', '$no_surat', '$tanggal_surat', '$tanggal_diterima', '$perihal')";
                $conn->query($sql);
                $inserted++;
            } else {
                $skipped++;
            }
        }

        $conn->close();
        $upload_berhasil = true;
    }
}

// Redirect jika berhasil
if ($upload_berhasil) {
    // Kirim jumlah yang dimasukkan dan dilewati via parameter GET
    header("Location: dashboard.php?upload=success&inserted=$inserted&skipped=$skipped");
    exit();
} else {
    echo "<script>
        alert('Gagal mengupload file atau format salah!');
        window.location.href = 'dashboard.php';
    </script>";
}
