<?php
// 1. Hubungkan dengan koneksi database
include 'koneksi.php';

// 2. Aktifkan session dan proteksi halaman admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan hanya admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// 3. Ambil data statistik riil dari database MySQL
$query_student = mysqli_query($conn, "SELECT * FROM students");
$total_students = mysqli_num_rows($query_student);

$query_lecturer = mysqli_query($conn, "SELECT * FROM lecturers");
$total_lecturers = mysqli_num_rows($query_lecturer);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div class="container-fluid shadow-sm px-4">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">
          <i class="bi bi-mortarboard-fill text-warning me-2"></i>University Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_students.php">Manage Students</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_lecturers.php">Manage Lecturers</a></li>
          </ul>
          <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-white-50 mb-0">
              <i class="bi bi-person-circle me-1"></i> Admin: <b><?= $_SESSION['username']; ?></b>
            </span>
            <a href="logout.php" class="btn btn-danger btn-sm px-3"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
          </div>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <div class="p-4 bg-white rounded shadow-sm border-start border-primary border-4">
                    <h2 class="fw-bold text-dark m-0">Dashboard Overview</h2>
                    <p class="text-muted m-0">Welcome back, Admin <strong><?= $_SESSION['username']; ?></strong>! Here is the latest update on your university campus data.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="card-title text-muted text-uppercase mb-1" style="font-size: 0.8rem; font-weight: 700;">Total Students</h6>
                            <h2 class="card-text fw-bold m-0"><?= $total_students; ?></h2>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 ps-3">
                        <a href="manage_students.php" class="text-decoration-none small text-primary fw-medium">Manage Students <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                            <i class="bi bi-person-badge-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="card-title text-muted text-uppercase mb-1" style="font-size: 0.8rem; font-weight: 700;">Total Lecturers</h6>
                            <h2 class="card-text fw-bold m-0"><?= $total_lecturers; ?></h2>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 ps-3">
                        <a href="manage_lecturers.php" class="text-decoration-none small text-success fw-medium">Manage Lecturers <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-4">
                <div class="card h-100 border-0 shadow-sm bg-dark text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-warning me-2"></i>System Security</h5>
                        <p class="card-text small text-white-50 mb-0">Database is connected via secure session handling and encrypted MD5 passwords.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold m-0 text-dark">Quick Action Panel</h5>
                    </div>
                    <div class="card-body pt-0">
                        <p class="text-muted small">Select an action below to fast-track your university database management:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="manage_students.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Add New Student</a>
                            <a href="manage_lecturers.php" class="btn btn-outline-success btn-sm"><i class="bi bi-plus-circle me-1"></i> Add New Lecturer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>