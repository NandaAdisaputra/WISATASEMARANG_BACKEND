<?php
$host = "localhost";
$user = "root"; // Sesuaikan dengan user database
$pass = ""; // Jika pakai XAMPP, kosongkan
$db   = "wisata_semarang"; // Sesuaikan dengan nama database

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}
?>