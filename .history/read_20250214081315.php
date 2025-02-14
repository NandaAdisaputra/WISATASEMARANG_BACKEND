<?php
include 'config.php';

// Atur header agar output berupa JSON dan bisa diakses dari perangkat lain
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Ambil IP server untuk membuat URL gambar
$server_ip = $_SERVER['SERVER_ADDR'];
$base_url = "http://" . $server_ip . "/";

// Tangkap parameter halaman & batas data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Query untuk menghitung total data
$total_query = "SELECT COUNT(*) as total FROM tempat_wisata";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Query untuk mengambil data terbaru dengan pagination
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata ORDER BY id DESC LIMIT ?, ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyiapkan query"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Bind parameter (gunakan "ii" untuk integer)
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
