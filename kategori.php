<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$pesan = "";

// --- LOGIKA TAMBAH KATEGORI (DENGAN CEK DUPLIKAT) ---
if (isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    
    // Cek apakah nama kategori sudah ada (Anti-Duplikat)
    $cek = mysqli_query($conn, "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "duplikat";
    } else {
        if (!empty($nama_kategori)) {
            $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
            if (mysqli_query($conn, $query)) { $pesan = "tambah_berhasil"; }
        }
    }
}

// --- LOGIKA EDIT KATEGORI ---
if (isset($_POST['edit_kategori'])) {
    $id_edit = $_POST['id_kategori'];
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $query_edit = "UPDATE kategori SET nama_kategori = '$nama_baru' WHERE id_kategori = '$id_edit'";
    if (mysqli_query($conn, $query_edit)) { $pesan = "edit_berhasil"; }
}

// --- LOGIKA HAPUS KATEGORI ---
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    $cek_penggunaan = mysqli_query($conn, "SELECT id_kategori FROM input_aspirasi WHERE id_kategori = '$id_hapus' LIMIT 1");
    
    if (mysqli_num_rows($cek_penggunaan) > 0) {
        $pesan = "gagal_hapus_digunakan";
    } else {
        if (mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = '$id_hapus'")) {
            header("Location: kategori.php?pesan=hapus_berhasil"); exit;
        }
    }
}

if (isset($_GET['pesan'])) { $pesan = $_GET['pesan']; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori | Padimes Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --soft-blue: #f0f7ff; --primary-blue: #3b82f6; --danger-red: #ef4444; }
        body { background: var(--soft-blue); font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; border-right: 1px solid rgba(59, 130, 246, 0.1); padding: 40px 24px; }
        .main-content { margin-left: 280px; padding: 50px; }
        .nav-link { color: #64748b; padding: 14px 18px; border-radius: 14px; margin-bottom: 8px; font-weight: 600; text-decoration: none; display: block; }
        .nav-link.active { background: var(--primary-blue); color: white; }
        .kategori-card { background: white; border-radius: 24px; padding: 25px; border: 1px solid #e2e8f0; transition: 0.3s; position: relative; }
        .icon-circle { width: 50px; height: 50px; background: rgba(59, 130, 246, 0.1); color: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 15px; }
        .action-btns { position: absolute; top: 15px; right: 15px; display: flex; gap: 5px; }
        .btn-action { border: none; background: none; color: #cbd5e1; transition: 0.3s; padding: 5px; }
        .btn-action.edit:hover { color: var(--primary-blue); }
        .btn-action.delete:hover { color: var(--danger-red); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-primary mb-5 px-3">PADIMES</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
            <a class="nav-link active" href="kategori.php"><i class="bi bi-tags-fill me-2"></i> Kategori</a>
            <a class="nav-link" href="history.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <hr class="my-4 opacity-50">
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold">Manajemen Kategori 🏷️</h2>
                <p class="text-muted">Kelola kategori yang muncul di halaman siswa.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-2"></i> Tambah Kategori
            </button>
        </div>

        <?php if($pesan == "tambah_berhasil"): ?>
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">✨ Berhasil menambahkan kategori baru!</div>
        <?php elseif($pesan == "edit_berhasil"): ?>
            <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4">📝 Nama kategori berhasil diubah!</div>
        <?php elseif($pesan == "duplikat"): ?>
            <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4">⚠️ Nama kategori tersebut sudah ada.</div>
        <?php elseif($pesan == "hapus_berhasil"): ?>
            <div class="alert alert-secondary border-0 rounded-4 shadow-sm mb-4">🗑️ Kategori berhasil dihapus.</div>
        <?php elseif($pesan == "gagal_hapus_digunakan"): ?>
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">❌ Tidak bisa hapus! Kategori masih digunakan dalam laporan.</div>
        <?php endif; ?>

        <div class="row g-4">
            <?php
            // Urutkan berdasarkan abjad agar rapi
            $q_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
            while($kat = mysqli_fetch_array($q_kategori)):
                $id_kat = $kat['id_kategori'];
                $hitung = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi WHERE id_kategori = '$id_kat'");
                $jumlah = mysqli_fetch_assoc($hitung)['total'];
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="kategori-card shadow-sm">
                    <div class="action-btns">
                        <button class="btn-action edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $id_kat ?>"><i class="bi bi-pencil-square"></i></button>
                        <a href="kategori.php?hapus=<?= $id_kat ?>" class="btn-action delete" onclick="return confirm('Hapus kategori ini?')"><i class="bi bi-trash3-fill"></i></a>
                    </div>
                    <div class="icon-circle"><i class="bi bi-tag-fill"></i></div>
                    <h5 class="fw-bold text-dark mb-1 text-capitalize"><?= $kat['nama_kategori'] ?></h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill"><?= $jumlah ?> Laporan</span>
                </div>
            </div>

            <div class="modal fade" id="modalEdit<?= $id_kat ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius: 24px;">
                        <form method="POST">
                            <div class="modal-header border-0 pt-4 px-4"><h5 class="fw-bold">Ubah Nama Kategori</h5></div>
                            <div class="modal-body px-4">
                                <input type="hidden" name="id_kategori" value="<?= $id_kat ?>">
                                <input type="text" name="nama_kategori" class="form-control" value="<?= $kat['nama_kategori'] ?>" required style="border-radius: 12px; padding: 12px;">
                            </div>
                            <div class="modal-footer border-0 pb-4 px-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="edit_kategori" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px;">
                <form method="POST">
                    <div class="modal-header border-0 pt-4 px-4"><h5 class="fw-bold">Tambah Kategori Baru</h5></div>
                    <div class="modal-body px-4">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Sarana Olahraga" required style="border-radius: 12px; padding: 12px;">
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_kategori" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>