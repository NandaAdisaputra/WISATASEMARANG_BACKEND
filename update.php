<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode tidak diizinkan"]);
    exit;
}

// Ambil data dari request
$id = $_POST['id'] ?? null;
$nama = $_POST['nama'] ?? null;
$lokasi = $_POST['lokasi'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? null;

// Validasi input
if (!$id || !$nama || !$lokasi || !$deskripsi) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Semua field harus diisi"]);
    exit;
}

// Periksa apakah tempat wisata dengan ID ini ada
$cek_stmt = $conn->prepare("SELECT gambar FROM tempat_wisata WHERE id = ?");
$cek_stmt->bind_param("i", $id);
$cek_stmt->execute();
$cek_result = $cek_stmt->get_result();
$cek_stmt->close();

if ($cek_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    exit;
}

// Persiapkan query update
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    // Path folder penyimpanan gambar
    $target_dir = "images/";
    $file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    // Validasi ekstensi
    if (!in_array($file_ext, $allowed_ext)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Format gambar harus JPG, JPEG, PNG, atau GIF"]);
        exit;
    }

    // Nama file unik
    $file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $file_name;

    // Simpan gambar
    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Gagal mengupload gambar"]);
        exit;
    }

    // Update dengan gambar baru
    $stmt = $conn->prepare("UPDATE tempat_wisata SET nama=?, lokasi=?, deskripsi=?, gambar=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama, $lokasi, $deskripsi, $target_file, $id);
} else {
    // Update tanpa gambar
    $stmt = $conn->prepare("UPDATE tempat_wisata SET nama=?, lokasi=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $lokasi, $deskripsi, $id);
}

// Eksekusi query
if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Data berhasil diupdate"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal mengupdate data"]);
}

$stmt->close();
?>
