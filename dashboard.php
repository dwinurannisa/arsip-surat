<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';
$conn = new mysqli($host, $username, $password, $dbname);

// Ambil jumlah surat per bidang
$query = "SELECT bidang, COUNT(*) AS jumlah FROM surat GROUP BY bidang";
$result = $conn->query($query);

$bidang = [];
$jumlah = [];
$ringkasan = [];
$totalSurat = 0;

while ($row = $result->fetch_assoc()) {
    $nama = $row['bidang'] ?: 'Tidak Ditentukan';
    $bidang[] = $nama;
    $jumlah[] = $row['jumlah'];
    $ringkasan[] = [$nama, $row['jumlah']];
    $totalSurat += $row['jumlah'];
}

// Ambil tanggal update terakhir
$updateTerakhir = "-";
$last = $conn->query("SELECT MAX(tanggal_diterima) AS terakhir FROM surat");
if ($last && $row = $last->fetch_assoc()) {
    $updateTerakhir = date('d M Y', strtotime($row['terakhir']));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Arsip Surat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Chart.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
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

        .main-content {
            padding: 30px;
        }

        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 20px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .info-box {
            text-align: left;
            font-size: 14px;
            margin-top: 20px;
        }

        .info-box ul {
            padding-left: 1rem;
        }

        .info-box li {
            margin-bottom: 5px;
        }

        h3 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .chart-container {
            width: 100%;
            max-height: 340px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px 10px;
            }

            .card-custom {
                padding: 15px;
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
                <a class="nav-link active" href="dashboard.php">📊 Dashboard</a>
                <a class="nav-link" href="upload_surat.php">📨 Upload Surat Masuk</a>
                <a class="nav-link" href="client.php">📁 Bidang I</a>
                <a class="nav-link" href="client2.php">📁 Bidang II</a>
                <a class="nav-link" href="client3.php">📁 Bidang III</a>
                <a class="nav-link" href="client4.php">📁 Bidang IV</a>
                <a class="nav-link mt-4" href="index.php">🔓 Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto main-content">
            <div class="card-custom text-center">
                <h3>Dashboard Arsip Surat</h3>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>

                <div class="info-box text-start mt-4">
                    <strong>Total Surat Masuk:</strong> <?= $totalSurat ?> surat<br>
                    <strong>Terakhir Diperbarui:</strong> <?= $updateTerakhir ?>
                    <hr>
                    <strong>Ringkasan Surat per Bidang:</strong>
                    <ul>
                        <?php foreach ($ringkasan as [$bid, $jml]) : ?>
                            <li><?= htmlspecialchars($bid) ?>: <?= $jml ?> surat</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($bidang) ?>,
            datasets: [{
                data: <?= json_encode($jumlah) ?>,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#20c997'
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
<?php $conn->close(); ?>
