<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$id = $_GET['id'];
// Ambil detail aspirasi
$query = mysqli_query($conn, "SELECT i.*, k.nama_kategori, s.nama_siswa FROM input_aspirasi i 
                              JOIN kategori k ON i.id_kategori = k.id_kategori 
                              JOIN siswa s ON i.nis = s.nis WHERE i.id_pelaporan = '$id'");
$data = mysqli_fetch_array($query);

// Ambil tanggapan lama jika sudah pernah diisi (untuk fitur Edit)
$cek_tanggapan = mysqli_query($conn, "SELECT * FROM tanggapan WHERE id_pelaporan = '$id'");
$tanggapan_lama = mysqli_fetch_array($cek_tanggapan);

if (isset($_POST['kirim_tanggapan'])) {
    $tanggapan_input = mysqli_real_escape_string($conn, $_POST['pesan_tanggapan']);
    $status = $_POST['status'];
    
    // 1. Simpan/Update menggunakan kolom 'tanggapan' sesuai database
    mysqli_query($conn, "INSERT INTO tanggapan (id_pelaporan, tanggapan, tanggal_tanggapan) 
                         VALUES ('$id', '$tanggapan_input', NOW()) 
                         ON DUPLICATE KEY UPDATE tanggapan = '$tanggapan_input', tanggal_tanggapan = NOW()");
    
    // 2. Update status di tabel input_aspirasi
    mysqli_query($conn, "UPDATE input_aspirasi SET status = '$status', tanggal_update = NOW() WHERE id_pelaporan = '$id'");
    
    header("Location: laporan.php?pesan=berhasil");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanggapi Aspirasi | Padimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f7ff; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card { border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid #e2e8f0; }
        .btn-primary { border-radius: 12px; padding: 12px 25px; font-weight: 600; background: #3b82f6; border: none; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card p-4 p-md-5">
                    <h4 class="fw-bold mb-4">Beri Tanggapan #<?= $id ?></h4>
                    
                    <div class="bg-light p-4 rounded-4 mb-4">
                        <p class="small text-muted mb-1">Aspirasi dari <b><?= $data['nama_siswa'] ?></b>:</p>
                        <h6 class="fw-bold">"<?= htmlspecialchars($data['ket']) ?>"</h6>
                        <hr>
                        <div class="small"><b>Lokasi:</b> <?= $data['lokasi'] ?> | <b>Kategori:</b> <?= $data['nama_kategori'] ?></div>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Pesan Balasan</label>
                            <textarea name="pesan_tanggapan" class="form-control" rows="4" placeholder="Ketik balasan untuk siswa..." required><?= $tanggapan_lama['tanggapan'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold mb-2">Ubah Status</label>
                            <select name="status" class="form-select">
                                <option value="Menunggu" <?= $data['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                                <option value="Proses" <?= $data['status']=='Proses'?'selected':'' ?>>Proses</option>
                                <option value="Selesai" <?= $data['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="laporan.php" class="btn btn-light px-4">Batal</a>
                            <button type="submit" name="kirim_tanggapan" class="btn btn-primary px-4">Kirim & Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>