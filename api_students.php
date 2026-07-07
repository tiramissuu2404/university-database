<?php
header('Content-Type: application/json');
include 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi API: Pastikan hanya admin yang bisa mengakses data
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// METHOD GET: Mengambil Seluruh Data Students
// ==========================================
if ($method === 'GET') {
    $res = mysqli_query($conn, "SELECT * FROM students");
    $students = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $students[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $students]);
    exit();
}

// Ambil input JSON dari raw body request (untuk POST, PUT, DELETE)
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// ==========================================
// ACTION: ADD STUDENT
// ==========================================
if ($action === 'add') {
    $nim   = mysqli_real_escape_string($conn, $input['student_id']);
    $name  = mysqli_real_escape_string($conn, $input['name']);
    $major = mysqli_real_escape_string($conn, $input['major']);
    $pass  = md5('student123');

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'NIM sudah terdaftar!']);
        exit();
    }

    $q_user = "INSERT INTO users (username, password, role) VALUES ('$nim', '$pass', 'student')";
    if (mysqli_query($conn, $q_user)) {
        $uid = mysqli_insert_id($conn);
        $q_student = "INSERT INTO students (student_id, user_id, name, major) VALUES ('$nim', '$uid', '$name', '$major')";
        if (mysqli_query($conn, $q_student)) {
            echo json_encode(['status' => 'success', 'message' => 'Student berhasil ditambahkan.']);
            exit();
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
    exit();
}

// ==========================================
// ACTION: EDIT STUDENT
// ==========================================
if ($action === 'edit') {
    $nim_lama = mysqli_real_escape_string($conn, $input['old_student_id']);
    $nim_baru = trim(mysqli_real_escape_string($conn, $input['student_id']));
    $name     = trim(mysqli_real_escape_string($conn, $input['name']));
    $major    = trim(mysqli_real_escape_string($conn, $input['major']));

    if ($nim_lama !== $nim_baru) {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$nim_baru'");
        if (mysqli_num_rows($cek) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'NIM baru sudah digunakan oleh akun lain!']);
            exit();
        }
    }

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE users SET username='$nim_baru' WHERE username='$nim_lama'");
        mysqli_query($conn, "UPDATE students SET student_id='$nim_baru', name='$name', major='$major' WHERE student_id='$nim_lama'");
        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Data student berhasil diperbarui.']);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
    }
    exit();
}

// ==========================================
// ACTION: DELETE STUDENT
// ==========================================
if ($action === 'delete') {
    $uid = mysqli_real_escape_string($conn, $input['user_id']);
    // ON DELETE CASCADE otomatis menghapus baris data di tabel students
    if (mysqli_query($conn, "DELETE FROM users WHERE id='$uid'")) {
        echo json_encode(['status' => 'success', 'message' => 'Data student berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data student.']);
    }
    exit();
}