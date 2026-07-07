<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include 'admin_dashboard.php'; ?>

    <div class="container mt-4">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-dark m-0"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Data Students</h3>
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Student
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle m-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="20%">NIM</th>
                            <th width="40%">Nama Lengkap</th>
                            <th width="25%">Jurusan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyStudents">
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form id="formTambahStudent" class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Tambah Student Baru</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label fw-bold">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" id="add_student_id" class="form-control" required placeholder="Contoh: 20250050043">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" id="add_name" class="form-control" required placeholder="Masukkan nama lengkap">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Jurusan (Major)</label>
                <input type="text" id="add_major" class="form-control" required placeholder="Contoh: Information System">
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4">Tambah</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form id="formEditStudent" class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Student</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_old_student_id">
            <div class="mb-3">
                <label class="form-label fw-bold">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" id="edit_student_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Jurusan</label>
                <input type="text" id="edit_major" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success px-3">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

    <script>
        // Instance modal Bootstrap untuk kontrol via JS
        const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
        const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));

        document.addEventListener("DOMContentLoaded", function() {
            loadStudentsData();

            // Event handler Tambah Student
            document.getElementById('formTambahStudent').addEventListener('submit', function(e) {
                e.preventDefault();
                const payload = {
                    action: 'add',
                    student_id: document.getElementById('add_student_id').value,
                    name: document.getElementById('add_name').value,
                    major: document.getElementById('add_major').value
                };

                sendRequest(payload, () => {
                    modalTambah.hide();
                    document.getElementById('formTambahStudent').reset();
                });
            });

            // Event handler Edit Student
            document.getElementById('formEditStudent').addEventListener('submit', function(e) {
                e.preventDefault();
                const payload = {
                    action: 'edit',
                    old_student_id: document.getElementById('edit_old_student_id').value,
                    student_id: document.getElementById('edit_student_id').value,
                    name: document.getElementById('edit_name').value,
                    major: document.getElementById('edit_major').value
                };

                sendRequest(payload, () => {
                    modalEdit.hide();
                });
            });
        });

        // Fungsi mengambil data dari API dan merender ke dalam tabel HTML
        function loadStudentsData() {
            fetch('api_students.php')
                .then(response => response.json())
                .then(res => {
                    const tbody = document.getElementById('tableBodyStudents');
                    tbody.innerHTML = '';

                    if(res.status === 'success' && res.data.length > 0) {
                        res.data.forEach(student => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="fw-bold">${student.student_id}</td>
                                <td>${student.name}</td>
                                <td>${student.major}</td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button class="btn btn-warning btn-sm rounded" onclick="openEditModal('${student.student_id}', '${student.name}', '${student.major}')">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm rounded" onclick="deleteStudent(${student.user_id}, '${student.name}')">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Belum ada data mahasiswa terdaftar.</td></tr>`;
                    }
                })
                .catch(err => console.error('Gagal memuat data:', err));
        }

        // Fungsi mengirimkan data mutasi (Add / Edit / Delete) ke API
        function sendRequest(payload, callbackSuccess) {
            fetch('api_students.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(res => {
                alert(res.message);
                if (res.status === 'success') {
                    if (callbackSuccess) callbackSuccess();
                    loadStudentsData(); // Refresh isi tabel tanpa reload halaman
                }
            })
            .catch(err => console.error('Request Gagal:', err));
        }

        // Fungsi untuk menembak parameter baris ke dalam form Modal Edit
        function openEditModal(id, name, major) {
            document.getElementById('edit_old_student_id').value = id;
            document.getElementById('edit_student_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_major').value = major;
            modalEdit.show();
        }

        // Fungsi Aksi Hapus Student
        function deleteStudent(userId, name) {
            if (confirm(`Yakin ingin menghapus student ${name}? Semua data akun terkait akan terhapus secara permanen.`)) {
                sendRequest({ action: 'delete', user_id: userId });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>