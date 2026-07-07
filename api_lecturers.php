<?php
header('Content-Type: application/json');
include 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi API: Pastikan hanya admin yang berhak memanipulasi data
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// ==========================================
// METHOD GET: Mengambil Semua Data Dosen
// ==========================================
if ($method === 'GET') {
    $res = mysqli_query($conn, "SELECT * FROM lecturers");
    $lecturers = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lecturers[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $lecturers]);
    exit();
}

// Ambil payload JSON dari request body
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// ==========================================
// ACTION: TAMBAH DOSEN
// ==========================================
if ($action === 'add') {
    $nidn   = mysqli_real_escape_string($conn, $input['lecturer_id']);
    $name   = mysqli_real_escape_string($conn, $input['name']);
    $major  = mysqli_real_escape_string($conn, $input['major']);
    $course = mysqli_real_escape_string($conn, $input['course']);
    $pass   = md5('lecturer123');

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$nidn'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'NIDN sudah terdaftar di sistem!']);
        exit();
    }

    $q_user = "INSERT INTO users (username, password, role) VALUES ('$nidn', '$pass', 'lecturer')";
    if (mysqli_query($conn, $q_user)) {
        $uid = mysqli_insert_id($conn);
        $q_lecturer = "INSERT INTO lecturers (lecturer_id, user_id, name, major, course) VALUES ('$nidn', '$uid', '$name', '$major', '$course')";
        if (mysqli_query($conn, $q_lecturer)) {
            echo json_encode(['status' => 'success', 'message' => 'Dosen berhasil ditambahkan.']);
            exit();
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
    exit();
}

// ==========================================
// ACTION: EDIT DATA DOSEN
// ==========================================
if ($action === 'edit') {
    $nidn_lama = mysqli_real_escape_string($conn, $input['old_lecturer_id']);
    $nidn_baru = trim(mysqli_real_escape_string($conn, $input['lecturer_id']));
    $name      = trim(mysqli_real_escape_string($conn, $input['name']));
    $major     = trim(mysqli_real_escape_string($conn, $input['major']));
    $course    = trim(mysqli_real_escape_string($conn, $input['course']));

    if ($nidn_lama !== $nidn_baru) {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$nidn_baru'");
        if (mysqli_num_rows($cek) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'NIDN baru sudah digunakan oleh akun lain!']);
            exit();
        }
    }

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE users SET username='$nidn_baru' WHERE username='$nidn_lama'");
        mysqli_query($conn, "UPDATE lecturers SET lecturer_id='$nidn_baru', name='$name', major='$major', course='$course' WHERE lecturer_id='$nidn_lama'");
        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Data dosen berhasil diperbarui.']);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data dosen.']);
    }
    exit();
}

// ==========================================
// ACTION: HAPUS DOSEN
// ==========================================
if ($action === 'delete') {
    $uid = mysqli_real_escape_string($conn, $input['user_id']);
    // Relasi ON DELETE CASCADE menangani penghapusan di tabel lecturers secara otomatis
    if (mysqli_query($conn, "DELETE FROM users WHERE id='$uid'")) {
        echo json_encode(['status' => 'success', 'message' => 'Data dosen berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data dari sistem.']);
    }
    exit();
}