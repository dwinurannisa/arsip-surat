<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require 'koneksi.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM surat WHERE bidang = 'Bidang I'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Arsip Surat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        .sidebar {
            height: 100vh;
            background-color: #004b8d;
            color: white;
            padding-top: 1.5rem;
        }
        .sidebar .nav-link {
            color: #fff;
            font-size: 14px;
            margin: 5px 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #00396b;
            border-radius: 6px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 10px 0;
            color: #6c757d;
        }
        #suratTable {
            font-size: 12px;
        }
        #suratTable .form-select {
            font-size: 12px;
            padding: 3px 8px;
            height: 30px;
            min-width: 120px;
            line-height: 1.2;
        }
        @media print {
            body {
                font-size: 11px;
                background: white;
                color: black;
            }
            #suratTable th, #suratTable td {
                padding: 3px 4px;
                border: 1px solid #000 !important;
            }
            .sidebar, .btn, form, .dropdown,
            .dataTables_filter, .dataTables_length,
            .dataTables_info, .dataTables_paginate,
            .dataTables_buttons {
                display: none !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="d-flex flex-column align-items-start px-3">
                <h5 class="text-white mb-3">📂 Menu</h5>
                <a class="nav-link" href="dashboard1.php">📊 Dashboard</a>
                <a class="nav-link" href="client.php">📁 Bidang I</a>
                <a class="nav-link" href="index.php">🔓 Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <h1 class="text-center mb-4">Data Arsip Surat</h1>

            <!-- Ekspor dan Cetak -->
            <div class="mb-4 d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-export"></i> Ekspor
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form id="exportForm" action="export_excel.php" method="POST" class="px-3 py-1">
                                <input type="hidden" name="id_list" id="id_list">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-file-excel"></i> Ekspor file
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Cetak Halaman
                </button>
            </div>

            <!-- Tabel -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="suratTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>NO. AGENDA</th>
                                <th>SURAT DARI</th>
                                <th>NO. SURAT</th>
                                <th>TANGGAL SURAT</th>
                                <th>TANGGAL DITERIMA</th>
                                <th>PERIHAL</th>
                                <th>BIDANG</th>
                                <th>STATUS</th>
                                <th>STATUS UPDATE</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                            <td><input type="checkbox" class="select-item" data-id="<?= $row['id'] ?>"></td>
                            <td><?= htmlspecialchars($row['no_agenda']) ?></td>
                            <td style="white-space: normal;"><?= nl2br(htmlspecialchars($row['surat_dari'])) ?></td>
                            <td><?= htmlspecialchars($row['no_surat']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_surat'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_diterima'])) ?></td>
                            <td style="white-space: normal;"><?= nl2br(htmlspecialchars($row['perihal'])) ?></td>
                            <td>
                                <select class="form-select" id="bidangSelect_<?= $row['id'] ?>" onchange="updateBidang(this, <?= $row['id'] ?>)">
                                    <option value="Bidang I" <?= $row['bidang'] == 'Bidang I' ? 'selected' : '' ?>>Bidang I</option>
                                </select>
                            </td>
                            <td>
                            <?php
                            $status = $row['status'];
                            if ($status === 'Diterima'):
                            ?>
                                <span class="badge bg-primary"><?= htmlspecialchars($status) ?></span>
                            <?php elseif ($status === 'Proses'): ?>
                                <span class="text-warning fw-semibold"><?= htmlspecialchars($status) ?></span>
                            <?php elseif ($status === 'Terkirim'): ?>
                                <select class="form-select" onchange="updateStatus(this, <?= $row['id'] ?>)" id="statusSelect_<?= $row['id'] ?>">
                                    <option selected disabled><?= $status ?></option>
                                    <option value="Diterima">Diterima</option>
                                </select>
                            <?php else: ?>
                                <span class="text-muted">Status tidak dikenal</span>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($row['tanggal_status_update']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    &copy; <?= date('Y') ?> Arsip Surat - Zeero. Semua Hak Cipta Dilindungi.
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
    function updateStatus(selectElement, id) {
        const status = selectElement.value;
        if (!status) return Swal.fire('Peringatan', 'Silakan pilih status.', 'warning');

        fetch('proses_update_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&status=${status}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = selectElement.closest('tr');
                row.querySelector('td:last-child').textContent = data.tanggal;
                if (status === 'Diterima') {
                    selectElement.outerHTML = '<span class="badge bg-primary">Diterima</span>';
                    const bidangSelect = row.querySelector('select[id^="bidangSelect_"]');
                    if (bidangSelect) bidangSelect.style.display = 'none';
                }
            } else {
                Swal.fire('Gagal', 'Gagal memperbarui status.', 'error');
            }
        });
    }

    function updateBidang(selectElement, id) {
        const bidang = selectElement.value;
        fetch('proses_update_bidang.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&bidang=${bidang}`
        }).then(r => r.text()).then(result => {
            if (result !== 'success') alert('Gagal memperbarui bidang.');
        });
    }

    $(document).ready(function () {
        const table = $('#suratTable').DataTable({
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>tip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            pageLength: 10,
            language: {
                search: "", searchPlaceholder: "🔍 Cari surat..."
            }
        });

        $('#selectAll').on('click', function () {
            $('.select-item').prop('checked', this.checked);
        });

        $('#exportForm').on('submit', function (e) {
            const selectedIds = $('.select-item:checked').map(function () {
                return $(this).data('id');
            }).get();
            if (!selectedIds.length) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Pilih surat yang ingin diekspor!', 'warning');
            } else {
                $('#id_list').val(selectedIds.join(','));
            }
        });
    });

    // Auto Reload Tiap 10 Detik
    setInterval(function () {
        location.reload();
    }, 10000); // 10 detik
</script>

<?php $conn->close(); ?>
</body>
</html>
