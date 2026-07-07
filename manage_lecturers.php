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
    <title>Manage Lecturers - University System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include 'admin_dashboard.php'; ?>

    <div class="container mt-4">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-dark m-0"><i class="bi bi-person-badge-fill text-success me-2"></i>Kelola Data Lecturers</h3>
                <button class="btn btn-success shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Lecturer
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle m-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="15%">NIDN</th>
                            <th width="30%">Nama Dosen</th>
                            <th width="20%">Jurusan</th>
                            <th width="20%">Mata Kuliah</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyLecturers">
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-success" role="status"></div> Loading data dosen...
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
        <form id="formTambahLecturer" class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Tambah Lecturer Baru</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label fw-bold">NIDN (Nomor Induk Dosen Nasional)</label>
                <input type="text" id="add_lecturer_id" class="form-control" required placeholder="Contoh: 19890101">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap & Gelar</label>
                <input type="text" id="add_name" class="form-control" required placeholder="Contoh: Falentino Sembiring, M.Kom.">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Jurusan (Major)</label>
                <input type="text" id="add_major" class="form-control" required placeholder="Contoh: Information System">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mata Kuliah (Course)</label>
                <input type="text" id="add_course" class="form-control" required placeholder="Contoh: Database Systems">
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success text-white px-4">Tambah</button>
          </div>
        </form>
      </div>
    </div>
<!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form id="formEditLecturer" class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Lecturer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_old_lecturer_id">
            <div class="mb-3">
                <label class="form-label fw-bold">NIDN (Nomor Induk Dosen Nasional)</label>
                <input type="text" id="edit_lecturer_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap & Gelar</label>
                <input type="text" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Jurusan</label>
                <input type="text" id="edit_major" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mata Kuliah Yang Diampu</label>
                <input type="text" id="edit_course" class="form-control" required>
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
        const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
        const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));

        document.addEventListener("DOMContentLoaded", function() {
            loadLecturersData();

            // Submit handler Tambah Dosen
            document.getElementById('formTambahLecturer').addEventListener('submit', function(e) {
                e.preventDefault();
                sendRequest({
                    action: 'add',
                    lecturer_id: document.getElementById('add_lecturer_id').value,
                    name: document.getElementById('add_name').value,
                    major: document.getElementById('add_major').value,
                    course: document.getElementById('add_course').value
                }, () => {
                    modalTambah.hide();
                    document.getElementById('formTambahLecturer').reset();
                });
            });

            // Submit handler Edit Dosen
            document.getElementById('formEditLecturer').addEventListener('submit', function(e) {
                e.preventDefault();
                sendRequest({
                    action: 'edit',
                    old_lecturer_id: document.getElementById('edit_old_lecturer_id').value,
                    lecturer_id: document.getElementById('edit_lecturer_id').value,
                    name: document.getElementById('edit_name').value,
                    major: document.getElementById('edit_major').value,
                    course: document.getElementById('edit_course').value
                }, () => {
                    modalEdit.hide();
                });
            });
        });

        // Menarik data JSON dan memetakannya ke tabel
        function loadLecturersData() {
            fetch('api_lecturers.php')
                .then(response => response.json())
                .then(res => {
                    const tbody = document.getElementById('tableBodyLecturers');
                    tbody.innerHTML = '';

                    if (res.status === 'success' && res.data.length > 0) {
                        res.data.forEach(lecturer => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="fw-bold">${lecturer.lecturer_id}</td>
                                <td>${lecturer.name}</td>
                                <td>${lecturer.major}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">${lecturer.course}</span></td>
                                <td class="text-center">
                                    <div class="btn-group gap-1">
                                        <button class="btn btn-warning btn-sm rounded" onclick="openEditModal('${lecturer.lecturer_id}', \`${lecturer.name}\`, '${lecturer.major}', '${lecturer.course}')">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm rounded" onclick="deleteLecturer(${lecturer.user_id}, \`${lecturer.name}\`)">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data dosen terdaftar.</td></tr>`;
                    }
                })
                .catch(err => console.error('Error fetching lecturers:', err));
        }

        // Pengirim request global ke backend
        function sendRequest(payload, callbackSuccess) {
            fetch('api_lecturers.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(res => {
                alert(res.message);
                if (res.status === 'success') {
                    if (callbackSuccess) callbackSuccess();
                    loadLecturersData(); // Refresh data tabel instan tanpa reload
                }
            })
            .catch(err => console.error('Mutation request failed:', err));
        }

        // Buka modal edit dan injeksikan nilainya
        function openEditModal(id, name, major, course) {
            document.getElementById('edit_old_lecturer_id').value = id;
            document.getElementById('edit_lecturer_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_major').value = major;
            document.getElementById('edit_course').value = course;
            modalEdit.show();
        }

        // Eksekusi fungsi hapus dosen
        function deleteLecturer(userId, name) {
            if (confirm(`Yakin ingin menghapus dosen ${name}? Akun login terkait akan ikut terhapus.`)) {
                sendRequest({ action: 'delete', user_id: userId });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>