<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

$host = "localhost"; // Sesuaikan dengan MySQL
$user = "root"; // Username MySQL
$pass = ""; // Password MySQL (kosongkan jika default)
$dbname = "wisata"; // Sesuaikan dengan database

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

// Tampilkan hasil dalam format JSON
echo json_encode(["status" => "success", "data" => $data]);

$conn->close();
?>
