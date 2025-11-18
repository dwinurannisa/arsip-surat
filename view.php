<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require 'koneksi.php';
$conn = new mysqli($host, $username, $password, $dbname);
$result = $conn->query("SELECT * FROM surat");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Arsip Surat - Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Styles and libraries (same as sebelumnya) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        h1 {
            color: #007bff;
            font-weight: 600;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            padding: 10px 0;
            background-color: #f8f9fc;
            color: #6c757d;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .login-icon {
            color: white;
            font-size: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        /* Responsif untuk tabel */
        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
            }

            .navbar-brand {
                font-size: 18px;
            }

            h1 {
                font-size: 24px;
            }

            .footer {
                font-size: 10px;
            }
        }

        /* Responsif untuk tampilan mobile */
        @media (max-width: 576px) {
            table th, table td {
                font-size: 10px;
            }

            .navbar {
                padding: 10px;
            }

            .container {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Arsip Surat</a>
        <a href="index.php" class="login-icon" title="Login">
            <i class="fas fa-sign-in-alt"></i> Logout
        </a>
    </div>
</nav>

<div class="container mb-5">
    <h1 class="text-center mb-4">Data Arsip Surat</h1>
    <div class="card">
        <div class="card-body table-responsive">
            <table id="suratTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>NO. AGENDA</th>
                        <th>SURAT DARI</th>
                        <th>NO. SURAT</th>
                        <th>TANGGAL SURAT</th>
                        <th>TANGGAL DITERIMA</th>
                        <th>PERIHAL</th>
                        <th>BIDANG</th>
                        <th>STATUS</th>
                        <th>TGL STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['no_agenda']) ?></td>
                            <td style="white-space: normal;"><?= nl2br(htmlspecialchars($row['surat_dari'])) ?></td>
                            <td><?= htmlspecialchars($row['no_surat']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                            <td><?= date('d F Y', strtotime($row['tanggal_diterima'])) ?></td>
                            <td style="white-space: normal;"><?= nl2br(htmlspecialchars($row['perihal'])) ?></td>
                            <td><?= htmlspecialchars($row['bidang']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?= !empty($row['tanggal_status_update']) ? date('d/m/Y', strtotime($row['tanggal_status_update'])) : '-' ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Arsip Surat - Zeero. Semua Hak Cipta Dilindungi.
</div>

<script>
    $(document).ready(function () {
        $('#suratTable').DataTable({
            language: {
                search: "",
                searchPlaceholder: "🔍 Cari surat..."
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
