<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];

    // Periksa apakah file diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        $file_name = basename($_FILES['gambar']['name']);
        $target_file = $target_dir . time() . "_" . $file_name; // Rename agar unik

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Simpan hanya PATH gambar ke database
            $stmt = $conn->prepare("INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $lokasi, $deskripsi, $target_file);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Data berhasil ditambahkan"]);
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