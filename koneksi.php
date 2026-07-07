<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "university";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengaktifkan session secara global
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>