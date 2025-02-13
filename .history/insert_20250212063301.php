<?php
require_once "db_config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_POST['gambar']; // Nama file dari upload.php

    // Simpan ke database
    $sql = "INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $lokasi, $deskripsi, $gambar);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan data"]);
    }
} else {
    echo json_encode(["error" => "Metode request tidak valid"]);
}
?>