<?php
session_start();
include('koneksi.php'); // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $sandi = isset($_POST['sandi']) ? $_POST['sandi'] : '';

    // Mencegah SQL Injection
    $username = $conn->real_escape_string($username);
    $sandi = $conn->real_escape_string($sandi);

    // Query untuk mencari pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifikasi password
        if ($row['sandi'] === $sandi) { // Gunakan password_verify jika hash
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Arahkan ke halaman sesuai peran
            if ($row['role'] == 'admin') {
                header("Location: dashboard.php");
                exit();
            } else if ($row['role'] == 'client1') {
                header("Location: client.php");
                exit();
            } else if ($row['role'] == 'client2') {
                header("Location: client2.php");
                exit();
            } else if ($row['role'] == 'client3') {
                header("Location: client3.php");
                exit();
            } else if ($row['role'] == 'client4') {
                header("Location: client4.php");
                exit();  
            } else if ($row['role'] == 'view') {
                header("Location: view.php");
                exit();  
            }            
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Link ke CDN Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            height: 100vh;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-container h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .login-container input {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
        
    </style>
</head>
<body>
<div class="login-container text-center">
            <h3>Selamat Datang</h3>
        <img src="img/logo.jpg" alt="Logo" style="width: 100px; margin-bottom: 20px;">
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required>
        </div>
        <div class="mb-3">
            <label for="sandi" class="form-label">Password</label>
            <input type="password" name="sandi" class="form-control" id="sandi" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
