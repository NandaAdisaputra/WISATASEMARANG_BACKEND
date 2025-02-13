<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Base URL otomatis (agar bisa digunakan di localhost & hosting)
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";

// Tangkap parameter halaman & batas data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Query untuk menghitung total data
$total_query = "SELECT COUNT(*) as total FROM tempat_wisata";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Query untuk mengambil data dengan pagination
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata LIMIT ?, ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyiapkan query"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Bind parameter (gunakan i untuk integer)
$stmt->bind_param("ii", $offset, $limit);
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

// Struktur JSON API dengan pagination
if (empty($data)) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak ditemukan",
        "data" => [],
        "pagination" => [
            "current_page" => $page,
            "total_pages" => $total_pages,
            "total_data" => $total_data
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil diambil",
        "data" => $data,
        "pagination" => [
            "current_page" => $page,
            "total_pages" => $total_pages,
            "total_data" => $total_data
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
?>
