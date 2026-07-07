<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

    
        session_write_close();

        // Mengirimkan response sukses berbentuk JSON
        echo json_encode([
            'status' => 'success', 
            'message' => 'Login Berhasil!', 
            'role' => $user['role']
        ]);
    } else {
        // Mengirimkan response gagal berbentuk JSON
        echo json_encode([
            'status' => 'error', 
            'message' => 'Username atau Password salah!'
        ]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow border-0 p-4" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold m-0 text-primary">University Login</h3>
            <small class="text-muted">Access your academic control dashboard</small>
        </div>
        
        <div id="alertPlaceholder"></div>

        <form id="formLogin">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Username (NIM/NIDN)</label>
                <input type="text" id="username" class="form-control" placeholder="Masukkan ID Anda" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top data-register-section">
            <p class="small text-muted mb-2">Belum memiliki akun log masuk?</p>
            <div class="d-flex justify-content-center gap-2 small">
                <a href="register_student.php" class="btn btn-outline-primary btn-sm px-2 py-1 rounded shadow-sm text-decoration-none">
                    <i class="bi bi-mortarboard-fill me-1"></i>Mahasiswa / Student
                </a>
                <span class="text-black-50 align-self-center">|</span>
                <a href="register_lecturer.php" class="btn btn-outline-success btn-sm px-2 py-1 rounded shadow-sm text-decoration-none">
                    <i class="bi bi-person-badge-fill me-1"></i>Dosen / Lecturer
                </a>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('formLogin').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData();
        formData.append('ajax_login', '1');
        formData.append('username', document.getElementById('username').value);
        formData.append('password', document.getElementById('password').value);

        // Mengirim data ke PHP menggunakan Fetch API (Asinkron)
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Menampung hasil ke dalam objek JSON
        .then(data => {
            const placeholder = document.getElementById('alertPlaceholder');
            if(data.status === 'success') {
                placeholder.innerHTML = `<div class="alert alert-success py-2 small shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> ${data.message} Mengalihkan...</div>`;
                
                // Matikan form agar tidak terjadi double submit saat proses tunggu redirect
                document.getElementById('username').disabled = true;
                document.getElementById('password').disabled = true;
                
                setTimeout(() => {
                    if(data.role === 'admin') {
                        window.location.href = 'admin_dashboard.php';
                    } else if(data.role === 'lecturer') {
                        window.location.href = 'lecturer_dashboard.php';
                    } else {
                        window.location.href = 'student_dashboard.php';
                    }
                }, 1200);
            } else {
                placeholder.innerHTML = `<div class="alert alert-danger py-2 small shadow-sm"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message}</div>`;
            }
        })
        .catch(err => {
            console.error('Request Login Error:', err);
        });
    });
    </script>
</body>
</html>