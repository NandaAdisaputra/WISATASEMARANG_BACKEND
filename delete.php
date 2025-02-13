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

// Ambil ID dari request
$id = $_POST['id'] ?? null;

// Validasi input
if (!$id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID harus diisi"]);
    exit;
}

// Cek apakah ID ada di database
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

// Ambil data gambar untuk dihapus
$row = $cek_result->fetch_assoc();
$gambar_path = $row['gambar'];

// Hapus data dari database
$stmt = $conn->prepare("DELETE FROM tempat_wisata WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Hapus gambar dari server jika ada
    if (!empty($gambar_path) && file_exists($gambar_path)) {
        unlink($gambar_path);
    }
    
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menghapus data"]);
}

$stmt->close();
?>
