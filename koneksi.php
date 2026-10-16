<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sekolah_aspirasi";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>