<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

// Mengambil nama kategori dari URL
$nama_kategori = isset($_GET['nama']) ? $_GET['nama'] : '';

// Query untuk mengambil aspirasi berdasarkan kategori yang dipilih
// Menggunakan LIKE karena di database formatnya 'Kategori | Sub'
$query = "SELECT s.nama_siswa, i.nis, a.lokasi, a.ket, a.status, i.tanggal_input 
          FROM input_aspirasi i 
          JOIN aspirasi a ON i.id_aspirasi = a.id_aspirasi 
          JOIN siswa s ON i.nis = s.nis 
          WHERE a.lokasi LIKE '$nama_kategori%'
          ORDER BY i.tanggal_input DESC";

$exec_query = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kategori: <?= htmlspecialchars($nama_kategori) ?> | Padimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --soft-blue: #f0f7ff; --primary-blue: #3b82f6; }
        body { background: var(--soft-blue); font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; border-right: 1px solid #e2e8f0; padding: 40px 24px; }
        .main-content { margin-left: 280px; padding: 50px; }
        .nav-link { color: #64748b; padding: 14px 18px; border-radius: 14px; margin-bottom: 8px; font-weight: 600; text-decoration: none; display: block; }
        .nav-link.active { background: var(--primary-blue); color: white; }
        .table-container { background: white; border-radius: 24px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-primary mb-5 px-3">PADIMES</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
            <a class="nav-link active" href="kategori.php"><i class="bi bi-tags-fill me-2"></i> Kategori</a>
            <a class="nav-link" href="history.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a>
            <hr class="my-4 opacity-50">
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex align-items-center gap-3 mb-5">
            <a href="kategori.php" class="btn btn-outline-primary border-0 rounded-circle"><i class="bi bi-arrow-left fs-4"></i></a>
            <div>
                <h2 class="fw-bold m-0">Kategori: <?= htmlspecialchars($nama_kategori) ?> 🏷️</h2>
                <p class="text-muted m-0">Menampilkan semua aspirasi di bagian ini.</p>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>TANGGAL</th>
                        <th>NIS / NAMA SISWA</th>
                        <th>SUB-LOKASI</th>
                        <th>ASPIRASI</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($exec_query) > 0): ?>
                        <?php while($row = mysqli_fetch_array($exec_query)): ?>
                        <tr>
                            <td class="small"><?= date('d M Y', strtotime($row['tanggal_input'])) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= $row['nis'] ?></div>
                                <div class="small text-muted"><?= $row['nama_siswa'] ?></div>
                            </td>
                            <td><span class="badge bg-light text-primary border rounded-pill"><?= $row['lokasi'] ?></span></td>
                            <td class="small w-25"><?= $row['ket'] ?></td>
                            <td>
                                <?php 
                                    $bg = $row['status'] == 'Selesai' ? 'success' : ($row['status'] == 'Proses' ? 'primary' : 'warning');
                                    echo "<span class='badge bg-$bg rounded-pill px-3'>{$row['status']}</span>";
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada aspirasi di kategori ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>