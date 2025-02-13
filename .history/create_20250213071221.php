<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];

    // Pastikan ada file yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        $file_name = time() . "_" . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $file_name; // Simpan path gambar

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Simpan path gambar ke database
            $stmt = $conn->prepare("INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $lokasi, $deskripsi, $target_file);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Data berhasil ditambahkan", "gambar" => $target_file]);
            } else {
                echo json_encode(["message" => "Gagal menambahkan data"]);
            }
            $stmt->close();
        } else {
            echo json_encode(["message" => "Gagal mengupload gambar"]);
        }
    } else {
        echo json_encode(["message" => "Gambar tidak ditemukan"]);
    }
}
?>