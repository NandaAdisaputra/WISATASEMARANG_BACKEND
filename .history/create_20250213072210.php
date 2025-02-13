<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Validasi input
    if (empty($nama) || empty($lokasi) || empty($deskripsi)) {
        echo json_encode(["message" => "Semua field harus diisi"]);
        exit;
    }

    // Pastikan ada file yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $target_dir = "images/";
        $file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (!in_array($file_ext, $allowed_extensions)) {
            echo json_encode(["message" => "Format gambar harus JPG, JPEG, PNG, atau GIF"]);
            exit;
        }

        // Gunakan nama unik untuk mencegah duplikasi
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        // Pindahkan file ke folder server
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
