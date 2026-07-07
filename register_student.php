<?php
include 'koneksi.php';

$success = ''; $error = '';
if (isset($_POST['register_student'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']); // NIM berfungsi sebagai username
    $password = md5($_POST['password']);
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $major    = mysqli_real_escape_string($conn, $_POST['major']);
    $role     = 'student';

    // Cek apakah NIM sudah terdaftar di tabel users
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "NIM sudah terdaftar sebagai akun!";
    } else {
        // 1. Insert ke tabel users
        $query_user = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if (mysqli_query($conn, $query_user)) {
            $user_id = mysqli_insert_id($conn);

            // 2. Insert ke tabel students
            mysqli_query($conn, "INSERT INTO students (student_id, user_id, name, major) VALUES ('$username', '$user_id', '$name', '$major')");
            
            header("Location: login.php?pesan=registrasi_berhasil");
            exit();
        } else {
            $error = "Registrasi Gagal, coba lagi!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Student - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh; padding: 20px 0;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">REGISTRASI MAHASISWA (STUDENT)</h5>
                </div>
                <div class="card-body p-4">
                    <?php if($error): ?> <div class="alert alert-danger"><?= $error; ?></div> <?php endif; ?>
                    <?php if($success): ?> <div class="alert alert-success"><?= $success; ?></div> <?php endif; ?>
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                            <input type="text" name="username" class="form-control" required placeholder="Contoh: 20250050043">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Buat password akun">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan (Major)</label>
                            <input type="text" name="major" class="form-control" required placeholder="Contoh: Information System">
                        </div>
                        <button type="submit" name="register_student" class="btn btn-primary w-100 py-2">Daftar Sebagai Mahasiswa</button>
                    </form>
                    <hr>
                    <div class="text-center small">
                        Sudah punya akun? <a href="login.php" class="text-decoration-none">Login</a> <br>
                        Mendaftar sebagai dosen? <a href="register_lecturer.php" class="text-decoration-none text-success font-weight-bold">Klik di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>