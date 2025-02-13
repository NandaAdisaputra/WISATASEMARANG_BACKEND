<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "wisata_semarang";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi Gagal: " . $conn->connect_error]));
}

$sql = "SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tambahkan URL gambar agar bisa diakses dari aplikasi Android
        $row['gambar'] = "http://localhost/Backend/uploads/" . $row['gambar'];
        $data[] = $row;
    }
}

echo json_encode(["status" => "success", "total" => count($data), "data" => $data], JSON_PRETTY_PRINT);

$conn->close();
?>
