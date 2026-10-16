<?php
session_start();
// Memastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat | Padimes Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f7ff; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; border-right: 1px solid rgba(59, 130, 246, 0.1); padding: 40px 24px; }
        .main-content { margin-left: 280px; padding: 50px; }
        
        .admin-status { background: #f8fafc; padding: 15px; border-radius: 16px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; border: 1px solid #e2e8f0; }
        .online-dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
        
        .nav-link { color: #64748b; padding: 14px; border-radius: 14px; margin-bottom: 5px; font-weight: 600; transition: 0.2s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #3b82f6; color: white; }
        
        .card-table { background: white; border-radius: 28px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(59, 130, 246, 0.05); }
        .badge-kategori { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-primary mb-4 px-2">PADIMES</h4>
        <div class="admin-status">
            <div class="online-dot"></div>
            <div class="small fw-bold">Admin Online</div>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
            <a class="nav-link" href="kategori.php"><i class="bi bi-tags-fill me-2"></i> Kategori</a>
            <a class="nav-link active" href="history.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a>
            <hr>
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-4">
            <h3 class="fw-bold text-dark">Riwayat Laporan</h3>
            <p class="text-muted">Daftar seluruh aspirasi yang telah masuk ke sistem.</p>
        </div>

        <div class="card-table">
            <table class="table table-hover align-middle">
                <thead class="text-muted small uppercase">
                    <tr>
                        <th class="border-0">SISWA & NIS</th>
                        <th class="border-0">KATEGORI</th>
                        <th class="border-0">LOKASI</th>
                        <th class="border-0">KETERANGAN</th>
                        <th class="border-0 text-center">TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query JOIN diperbaiki: Menghubungkan input_aspirasi ke siswa dan kategori
                    $q = mysqli_query($conn, "SELECT i.*, s.nama_siswa, k.nama_kategori 
                                             FROM input_aspirasi i 
                                             JOIN siswa s ON i.nis = s.nis 
                                             JOIN kategori k ON i.id_kategori = k.id_kategori 
                                             ORDER BY i.id_pelaporan DESC");
                    
                    if(mysqli_num_rows($q) > 0):
                        while($row = mysqli_fetch_array($q)):
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark"><?= $row['nama_siswa'] ?></span>
                                <small class="text-muted"><?= $row['nis'] ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-kategori rounded-pill px-3 py-2">
                                <?= $row['nama_kategori'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-dark fw-medium"><?= $row['lokasi'] ?></span>
                        </td>
                        <td class="small text-muted">
                            <?= $row['ket'] ?>
                        </td>
                        <td class="text-center small text-muted">
                            <?= date('d M Y', strtotime($row['tanggal_input'])) ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat laporan yang masuk.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>