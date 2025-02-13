<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Base URL otomatis (agar bisa digunakan di localhost & hosting)
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";

// Query untuk mengambil data tempat wisata
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata");

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyiapkan query"
    ], JSON_PRETTY_PRINT);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Jika gambar tersedia, tambahkan URL lengkap
    if (!empty($row['gambar'])) {
        $row['gambar'] = $base_url . ltrim($row['gambar'], '/'); 
    }
    $data[] = $row;
}

// Tutup statement
$stmt->close();

// Struktur JSON API dengan status & pesan
if (empty($data)) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak ditemukan",
        "data" => []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil diambil",
        "data" => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
?>
