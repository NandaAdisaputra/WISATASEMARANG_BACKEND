<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Base URL otomatis (agar bisa digunakan di localhost & hosting)
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";

// Query untuk mengambil data tempat wisata
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata");

if (!$stmt) {
    echo json_encode(["error" => "Gagal menyiapkan query"], JSON_PRETTY_PRINT);
    http_response_code(500);
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

// Jika data kosong, beri respons 404
if (empty($data)) {
    echo json_encode(["message" => "Data tidak ditemukan"], JSON_PRETTY_PRINT);
    http_response_code(404);
} else {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
?>
