<?php
session_start();
include 'koneksi.php';
if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];
    $query = mysqli_query($conn, "SELECT * FROM Admin WHERE username='$user' AND password='$pass'");
    if (mysqli_num_rows($query) > 0) {
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
    } else { $error = "Username atau Password salah!"; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin | Padimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f0f7ff 0%, #e0e7ff 100%); font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; display: flex; align-items: center; }
        .login-card { background: white; border: none; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.15); width: 100%; max-width: 420px; margin: auto; padding: 40px; }
        .form-control { border-radius: 12px; padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .btn-primary { background: #3b82f6; border: none; border-radius: 12px; padding: 12px; font-weight: 700; transition: 0.3s; }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">PADIMES</h2>
            <p class="text-muted">Panel Administrasi Sekolah</p>
        </div>
        <?php if(isset($error)): ?><div class="alert alert-danger border-0 small text-center"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin123" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 shadow-sm">Masuk Sekarang</button>
        </form>
    </div>
</body>
</html>