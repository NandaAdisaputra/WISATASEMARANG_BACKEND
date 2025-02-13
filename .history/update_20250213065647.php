<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    
    if (isset($_FILES['gambar'])) {
        $gambar = file_get_contents($_FILES['gambar']['tmp_name']);
        $sql = "UPDATE tempat_wisata SET nama=?, lokasi=?, deskripsi=?, gambar=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama, $lokasi, $deskripsi, $gambar, $id);
    } else {
        $sql = "UPDATE tempat_wisata SET nama=?, lokasi=?, deskripsi=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $lokasi, $deskripsi, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Data berhasil diupdate"]);
    } else {
        echo json_encode(["message" => "Gagal mengupdate data"]);
    }
    $stmt->close();
}
?>