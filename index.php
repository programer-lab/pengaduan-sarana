<?php
include 'koneksi.php';
$pesan = "";

// Logika pengiriman aspirasi
if (isset($_POST['kirim'])) {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $id_kategori = isset($_POST['id_kategori']) ? mysqli_real_escape_string($conn, $_POST['id_kategori']) : '';
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $ket = mysqli_real_escape_string($conn, $_POST['ket']);

    // Validasi NIS siswa
    $cek_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    
    if ($cek_siswa && mysqli_num_rows($cek_siswa) > 0) {
        if (!empty($id_kategori)) {
            $q = "INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, status, tanggal_input, tanggal_update) 
                  VALUES ('$nis', '$id_kategori', '$lokasi', '$ket', 'Menunggu', NOW(), NOW())";
            
            if (mysqli_query($conn, $q)) {
                $id_pelaporan = mysqli_insert_id($conn);
                $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-4'>✨ <b>Berhasil!</b> Aspirasi terkirim dengan ID Pelaporan: #$id_pelaporan</div>";
            } else {
                $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4'>❌ <b>Error Database:</b> " . mysqli_error($conn) . "</div>";
            }
        } else {
            $pesan = "<div class='alert alert-warning border-0 shadow-sm rounded-4'>⚠️ <b>Gagal!</b> Silakan pilih kategori terlebih dahulu.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger border-0 shadow-sm rounded-4'>❌ <b>Gagal!</b> NIS tidak ditemukan. Pastikan Anda sudah terdaftar.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padimes | Aspirasi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --soft-blue: #F0F7FF; --primary-blue: #4A89FF; }
        body { background: var(--soft-blue); font-family: 'Plus Jakarta Sans', sans-serif; color: #2D3748; }
        .glass-card { background: white; border: none; border-radius: 30px; box-shadow: 0 20px 40px rgba(74, 137, 255, 0.1); overflow: hidden; }
        .nav-pills .nav-link { border-radius: 12px; color: #718096; font-weight: 600; padding: 12px 25px; transition: 0.3s; }
        .nav-pills .nav-link.active { background: var(--primary-blue); box-shadow: 0 10px 20px rgba(74, 137, 255, 0.2); color: white; }
        .form-control, .form-select { border-radius: 15px; padding: 14px; border: 1px solid #E2E8F0; background: #F8FAFC; transition: 0.3s; }
        .btn-send { background: var(--primary-blue); color: white; border-radius: 15px; padding: 14px; font-weight: 700; border: none; width: 100%; transition: 0.3s; cursor: pointer; }
        .btn-send:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(74, 137, 255, 0.3); color: white; }
        .time-text { font-size: 11px; color: #a0aec0; }
        .riwayat-item { background: white; border: 1px solid #edf2f7; border-radius: 20px; padding: 20px; margin-bottom: 15px; transition: 0.2s; }
        .status-badge { font-size: 10px; font-weight: 700; padding: 5px 12px; border-radius: 10px; text-transform: uppercase; }
        .status-menunggu { background: #FFF5F5; color: #E53E3E; }
        .status-proses { background: #EBF8FF; color: #3182CE; }
        .status-selesai { background: #F0FFF4; color: #38A169; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary" style="letter-spacing: -1.5px;">PENGADUAN SARANA SEKOLAH</h1>
        <p class="text-muted">Suarakan aspirasimu untuk sekolah yang lebih baik</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="glass-card p-4 p-md-5">
                
                <ul class="nav nav-pills mb-5 justify-content-center bg-light p-2 rounded-4" id="pills-tab" role="tablist">
                    <li class="nav-item w-50">
                        <button class="nav-link active w-100" id="buat-tab" data-bs-toggle="pill" data-bs-target="#buat-aspi">Sampaikan</button>
                    </li>
                    <li class="nav-item w-50">
                        <button class="nav-link w-100" id="cek-tab" data-bs-toggle="pill" data-bs-target="#cek-aspi">Cek Riwayat</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="buat-aspi">
                        <?= $pesan ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="small fw-bold mb-2">NIS Siswa</label>
                                <input type="number" name="nis" class="form-control" placeholder="Masukan NIS Anda" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold mb-2">Kategori</label>
                                    <select name="id_kategori" class="form-select" required>
                                        <option value="" disabled selected>Pilih Kategori</option>
                                        <?php
                                        $kats = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                        while($k = mysqli_fetch_array($kats)):
                                        ?>
                                            <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small fw-bold mb-2">Lokasi Terkait</label>
                                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lab Komputer" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold mb-2">Detail Aspirasi</label>
                                <textarea name="ket" class="form-control" rows="4" placeholder="Jelaskan secara detail..." required></textarea>
                            </div>
                            <button type="submit" name="kirim" class="btn btn-send">Kirim Aspirasi <i class="bi bi-send-fill ms-2"></i></button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="cek-aspi">
                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="number" name="search_nis" class="form-control shadow-none" placeholder="Masukkan NIS Anda" value="<?= htmlspecialchars(@$_GET['search_nis']) ?>" required>
                                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>

                        <div class="riwayat-list">
                            <?php
                            if(isset($_GET['search_nis'])){
                                $snis = mysqli_real_escape_string($conn, $_GET['search_nis']);
                                
                                // QUERY DIPERBAIKI: Menggunakan LEFT JOIN ke tabel tanggapan agar feedback admin muncul
                                $riwayat = mysqli_query($conn, "SELECT i.*, k.nama_kategori, t.tanggapan, t.tanggal_tanggapan 
                                           FROM input_aspirasi i
                                           JOIN kategori k ON i.id_kategori = k.id_kategori 
                                           LEFT JOIN tanggapan t ON i.id_pelaporan = t.id_pelaporan
                                           WHERE i.nis='$snis' 
                                           ORDER BY i.id_pelaporan DESC");
                                
                                if($riwayat && mysqli_num_rows($riwayat) > 0){
                                    while($row = mysqli_fetch_array($riwayat)):
                                        $tanggal = date('d M Y, H:i', strtotime($row['tanggal_input']));
                                        $status_class = "status-" . strtolower($row['status']);
                            ?>
                                    <div class="riwayat-item shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="small fw-bold text-primary d-block">#<?= $row['id_pelaporan'] ?></span>
                                                <span class="time-text"><i class="bi bi-calendar3 me-1"></i><?= $tanggal ?></span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary bg-opacity-10 text-primary border-0 rounded-pill px-3 py-2 mb-1 d-block"><?= $row['nama_kategori'] ?></span>
                                                <span class="status-badge <?= $status_class ?>"><?= $row['status'] ?></span>
                                            </div>
                                        </div>
                                        <p class="small m-0 text-dark fw-bold mt-2"><?= $row['lokasi'] ?></p>
                                        <p class="small m-0 text-muted">"<?= htmlspecialchars($row['ket']) ?>"</p>

                                        <?php if(!empty($row['tanggapan'])): ?>
                                            <div class="mt-3 p-3 bg-light rounded-4 border-start border-primary border-4" style="font-size: 0.85rem;">
                                                <b class="text-primary d-block mb-1"><i class="bi bi-reply-fill"></i> Balasan Admin:</b>
                                                <p class="m-0 text-dark">"<?= htmlspecialchars($row['tanggapan']) ?>"</p>
                                                <small class="time-text mt-2 d-block">Dibalas pada: <?= date('d/m/Y H:i', strtotime($row['tanggal_tanggapan'])) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                            <?php 
                                    endwhile; 
                                } else { 
                                    echo "<div class='text-center py-4'><i class='bi bi-search text-muted' style='font-size: 2rem;'></i><p class='text-muted small mt-2'>Belum ada riwayat untuk NIS ini.</p></div>"; 
                                } 
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Memastikan tab riwayat tetap aktif setelah pencarian NIS
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('search_nis')) {
        const trigger = document.querySelector('#cek-tab');
        if(trigger) {
            const tab = new bootstrap.Tab(trigger);
            tab.show();
        }
    }
</script>
</body>
</html>