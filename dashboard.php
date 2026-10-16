<?php
session_start();
// Memastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

// Statistik Total Aspirasi (Karena status sudah dihapus, kita hitung total data)
$total_aspirasi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM input_aspirasi"));
$hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM input_aspirasi WHERE DATE(tanggal_input) = CURDATE()"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Admin | Padimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --soft-blue: #f0f7ff; --primary-blue: #3b82f6; --dark-blue: #1e40af; }
        body { background: var(--soft-blue); font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        .sidebar { 
            width: 280px; background: white; height: 100vh; position: fixed; 
            border-right: 1px solid rgba(59, 130, 246, 0.1); padding: 40px 24px;
        }
        
        .admin-profile {
            background: #f8fafc; border-radius: 16px; padding: 15px; margin-bottom: 30px;
            border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;
        }
        .online-dot {
            width: 10px; height: 10px; background: #22c55e; border-radius: 50%;
            display: inline-block; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .main-content { margin-left: 280px; padding: 50px; }
        
        .stat-card {
            border: none; border-radius: 24px; padding: 25px; transition: all 0.3s ease;
            background: white; border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.05);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.1); }
        
        .icon-box {
            width: 50px; height: 50px; border-radius: 14px; 
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }

        .nav-link { 
            color: #64748b; padding: 14px 18px; border-radius: 14px; 
            margin-bottom: 8px; font-weight: 600; transition: 0.2s; text-decoration: none; display: block;
        }
        .nav-link:hover, .nav-link.active { background: var(--primary-blue); color: white; }
        
        .table-container { background: white; border-radius: 28px; padding: 30px; border: 1px solid #e2e8f0; }
        .btn-action { border-radius: 12px; font-weight: 600; padding: 8px 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-primary mb-5 px-3">PADIMES</h4>
        
        <div class="admin-profile">
            <div class="position-relative">
                <img src="https://ui-avatars.com/api/?name=Admin&background=3b82f6&color=fff" class="rounded-circle" width="40">
                <span class="online-dot position-absolute bottom-0 end-0"></span>
            </div>
            <div>
                <p class="m-0 small fw-bold text-dark"><?= $_SESSION['admin'] ?></p>
                <small class="text-success fw-semibold" style="font-size: 10px;">• Online Sekarang</small>
            </div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
            <a class="nav-link" href="kategori.php"><i class="bi bi-tags-fill me-2"></i> Kategori</a>
            <a class="nav-link" href="history.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan</a>
            <hr class="my-4 opacity-50">
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="fw-bold">Halo, Admin 👋</h2>
            <p class="text-muted">Berikut adalah ringkasan aspirasi siswa yang masuk.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <small class="text-muted fw-bold">TOTAL ASPIRASI</small>
                            <h3 class="fw-bold m-0"><?= $total_aspirasi ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-calendar-check"></i></div>
                        <div>
                            <small class="text-muted fw-bold">MASUK HARI INI</small>
                            <h3 class="fw-bold m-0"><?= $hari_ini ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <h5 class="fw-bold m-0">Daftar Aspirasi Terbaru</h5>
                <span class="badge bg-soft-blue text-primary border px-3 py-2 rounded-pill">Total: <?= $total_aspirasi ?></span>
            </div>
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small uppercase">
                        <th class="border-0 ps-4">Siswa & NIS</th>
                        <th class="border-0">Kategori</th>
                        <th class="border-0">Lokasi</th>
                        <th class="border-0">Keterangan</th>
                        <th class="border-0 text-center">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query JOIN yang benar sesuai struktur tabel terbaru:
                    // input_aspirasi disambungkan ke siswa (lewat NIS) dan kategori (lewat id_kategori)
                    $data = mysqli_query($conn, "SELECT i.*, s.nama_siswa, k.nama_kategori 
                            FROM input_aspirasi i
                            JOIN siswa s ON i.nis = s.nis
                            JOIN kategori k ON i.id_kategori = k.id_kategori
                            ORDER BY i.id_pelaporan DESC");
                    
                    while($r = mysqli_fetch_array($data)):
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark"><?= $r['nama_siswa'] ?></span>
                                <small class="text-muted"><?= $r['nis'] ?></small>
                            </div>
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><?= $r['nama_kategori'] ?></span></td>
                        <td><span class="text-dark fw-semibold"><?= $r['lokasi'] ?></span></td>
                        <td class="small text-muted"><?= (strlen($r['ket']) > 50) ? substr($r['ket'],0,50).'...' : $r['ket'] ?></td>
                        <td class="text-center small text-muted">
                            <?= date('d/m/Y', strtotime($r['tanggal_input'])) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>