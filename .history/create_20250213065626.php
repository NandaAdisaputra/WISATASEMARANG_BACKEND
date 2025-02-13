<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = file_get_contents($_FILES['gambar']['tmp_name']);

    $stmt = $conn->prepare("INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssb", $nama, $lokasi, $deskripsi, $gambar);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data berhasil ditambahkan"]);
    } else {
        echo json_encode(["message" => "Gagal menambahkan data"]);
    }
    $stmt->close();
}
?>