<?php
require_once "db_config.php";

$sql = "SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    // Tambahkan URL lengkap gambar
    $row['gambar_url'] = "http://localhost/backend/images/" . $row['gambar'];
    $data[] = $row;
}

echo json_encode($data);
?>