<?php
require_once "db_config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_POST['gambar']; // Nama file baru

    // Update data
    $sql = "UPDATE tempat_wisata SET nama=?, lokasi=?, deskripsi=?, gambar=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nama, $lokasi, $deskripsi, $gambar, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data berhasil diperbarui"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui data"]);
    }
} else {
    echo json_encode(["error" => "Metode request tidak valid"]);
}
?>