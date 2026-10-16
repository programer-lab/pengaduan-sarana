<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

// Filter
$f_nis    = isset($_GET['nis']) ? mysqli_real_escape_string($conn, $_GET['nis']) : '';
$f_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Query Utama dengan LEFT JOIN ke tabel tanggapan
$query = "SELECT i.*, s.nama_siswa, k.nama_kategori, t.tanggapan, t.tanggal_tanggapan
          FROM input_aspirasi i 
          JOIN siswa s ON i.nis = s.nis 
          JOIN kategori k ON i.id_kategori = k.id_kategori 
          LEFT JOIN tanggapan t ON i.id_pelaporan = t.id_pelaporan
          WHERE 1=1";

if($f_nis != '') $query .= " AND i.nis = '$f_nis'";
if($f_status != '') $query .= " AND i.status = '$f_status'";

$query .= " ORDER BY i.tanggal_update DESC";
$result = mysqli_query($conn, $query);

// Cek error untuk mencegah Fatal Error muncul di layar
if (!$result) {
    die("Kesalahan Database: " . mysqli_error($conn) . ". Pastikan kolom 'tanggapan' sudah ada di tabel.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan | Padimes Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --soft-blue: #f0f7ff; --primary-blue: #3b82f6; }
        body { background: var(--soft-blue); font-family: sans-serif; }
        .sidebar { width: 260px; background: white; height: 100vh; position: fixed; border-right: 1px solid #eef2f7; padding: 40px 20px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.02); padding: 20px; }
        .badge-status { padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 10px; }
        .status-menunggu { background: #f1f5f9; color: #64748b; }
        .status-proses { background: #fffbeb; color: #d97706; }
        .status-selesai { background: #f0fdf4; color: #16a34a; }
        .feedback-box { font-size: 12px; background: #f8fafc; border-left: 4px solid #3b82f6; padding: 10px; border-radius: 6px; color: #334155; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="fw-bold text-primary mb-5">PADIMES</h3>
        <a href="dashboard.php" class="nav-link text-muted mb-3"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a href="laporan.php" class="nav-link text-primary fw-bold mb-3"><i class="bi bi-file-earmark-text me-2"></i> Laporan</a>
        <a href="logout.php" class="nav-link text-danger mt-5"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
    </div>

    <div class="main-content">
        <h3 class="fw-bold mb-4">Daftar Aspirasi & Feedback Admin</h3>
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th>Update</th>
                            <th>Siswa</th>
                            <th>Aspirasi</th>
                            <th>Feedback Admin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_array($result)): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($row['tanggal_update'])) ?></div>
                                <div class="small text-muted"><?= date('H:i', strtotime($row['tanggal_update'])) ?> WIB</div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= $row['nama_siswa'] ?></div>
                                <div class="small text-muted"><?= $row['nis'] ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary mb-1"><?= $row['nama_kategori'] ?></span>
                                <div class="small text-muted text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['ket']) ?></div>
                            </td>
                            <td style="min-width: 220px;">
                                <?php if(!empty($row['tanggapan'])): ?>
                                    <div class="feedback-box">
                                        <strong>Balasan:</strong><br>
                                        <?= htmlspecialchars($row['tanggapan']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small"><i>Belum ditanggapi</i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status status-<?= strtolower($row['status']) ?>">● <?= strtoupper($row['status']) ?></span>
                            </td>
                            <td>
                                <a href="tanggapi.php?id=<?= $row['id_pelaporan'] ?>" class="btn btn-sm <?= !empty($row['tanggapan']) ? 'btn-success' : 'btn-outline-primary' ?> rounded-pill px-3">
                                    <?= !empty($row['tanggapan']) ? 'Edit' : 'Tanggapi' ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>