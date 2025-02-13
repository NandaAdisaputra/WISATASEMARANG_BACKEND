<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$host = "localhost"; // Sesuaikan dengan MySQL
$user = "root"; // Username MySQL
$pass = ""; // Password MySQL (kosongkan jika default)
$dbname = "wisata_semarang"; // Sesuaikan dengan database

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi Gagal: " . $conn->connect_error]));
}

// Ambil data dari tabel
$sql = "SELECT * FROM tempat_wisata";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Tampilkan hasil dalam format JSON
// echo json_encode(["status" => "success", "data" => $data]);
echo json_encode(["status" => "success", "total" => count($data), "data" => $data], JSON_PRETTY_PRINT);
$conn->close();
?>
