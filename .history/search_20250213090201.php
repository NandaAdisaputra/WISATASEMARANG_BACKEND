<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Pastikan request adalah GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode tidak diizinkan"]);
    exit;
}

// Ambil kata kunci dari parameter GET
$keyword = $_GET['q'] ?? '';

// Validasi input
if (empty($keyword)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Kata kunci pencarian harus diisi"]);
    exit;
}

// Query pencarian dengan LIKE
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata 
                        WHERE nama LIKE ? OR lokasi LIKE ? OR deskripsi LIKE ?");
$like_keyword = "%" . $keyword . "%";
$stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data hasil pencarian
$data = [];
while ($row = $result->fetch_assoc()) {
    // Tambahkan URL lengkap untuk gambar jika ada
    if (!empty($row['gambar'])) {
        $row['gambar'] = "http://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($row['gambar'], '/');
    }
    $data[] = $row;
}

// Tutup statement
$stmt->close();

// Tampilkan hasil dalam format JSON
if (empty($data)) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
} else {
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Data ditemukan", "data" => $data], JSON_PRETTY_PRINT);
}
?>
