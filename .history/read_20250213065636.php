<?php
include 'config.php';
$result = $conn->query("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata");
$data = [];
while ($row = $result->fetch_assoc()) {
    $row['gambar'] = "http://localhost/images/" . $row['id'] . ".jpg";
    $data[] = $row;
}
echo json_encode($data);
?>