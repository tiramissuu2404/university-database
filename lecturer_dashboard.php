<?php
include 'koneksi.php';
if ($_SESSION['role'] != 'lecturer') { header("Location: login.php"); exit(); }

$uid = $_SESSION['user_id'];
$query = "SELECT * FROM lecturers WHERE user_id = '$uid'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5>LECTURER PORTAL</h5>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
                <div class="card-body">
                    <h3>Selamat Datang, <?= $data['name']; ?>!</h3>
                    <p class="text-muted">Berikut info data mengajar Anda:</p>
                    <table class="table table-bordered">
                        <tr><th>NIDN</th><td><?= $data['lecturer_id']; ?></td></tr>
                        <tr><th>Nama Dosen</th><td><?= $data['name']; ?></td></tr>
                        <tr><th>Fakultas/Jurusan</th><td><?= $data['major']; ?></td></tr>
                        <tr><th>Mata Kuliah Diampu</th><td><b><?= $data['course']; ?></b></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>