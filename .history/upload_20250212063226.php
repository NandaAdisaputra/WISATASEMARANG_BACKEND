<?php
require_once "db_config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['gambar'])) {
        $target_dir = "images/"; // Folder penyimpanan gambar
        $file_name = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;

        // Simpan file di folder
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            echo json_encode(["message" => "Gambar berhasil diunggah", "file_name" => $file_name]);
        } else {
            echo json_encode(["error" => "Gagal mengunggah gambar"]);
        }
    } else {
        echo json_encode(["error" => "Tidak ada gambar yang dikirim"]);
    }
} else {
    echo json_encode(["error" => "Metode request tidak valid"]);
}
?>
