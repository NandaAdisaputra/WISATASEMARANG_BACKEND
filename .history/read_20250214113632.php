<?php
// Mengimpor file konfigurasi database
include 'config.php';

// Mengatur header agar output berupa JSON dan dapat diakses dari perangkat lain (CORS)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Mengizinkan akses dari semua domain
header("Access-Control-Allow-Methods: GET, POST"); // Mengizinkan metode GET dan POST
header("Access-Control-Allow-Headers: Content-Type"); // Mengizinkan pengiriman data dengan format JSON

// Mendapatkan IP server untuk membentuk URL gambar
$server_ip = $_SERVER['SERVER_ADDR']; // IP server tempat file disimpan
$base_url = "http://" . $server_ip . "/"; // Base URL untuk gambar

// Mengambil parameter pagination dari URL, jika tidak ada gunakan nilai default
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Halaman default adalah 1
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10; // Jumlah data per halaman, default 10
$offset = ($page - 1) * $limit; // Menghitung posisi awal data untuk query

// Query untuk menghitung total jumlah data dalam tabel
$total_query = "SELECT COUNT(*) as total FROM tempat_wisata";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total']; // Mendapatkan jumlah total data
$total_pages = ceil($total_data / $limit); // Menghitung jumlah halaman berdasarkan limit

// Query untuk mengambil data dengan pagination dan mengurutkan berdasarkan ID terbaru
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata ORDER BY id DESC LIMIT ?, ?");
if (!$stmt) {
    // Jika terjadi kesalahan dalam persiapan query, kirimkan respon error
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyiapkan query"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Bind parameter ke query (ii -> dua parameter bertipe integer)
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result(); // Menjalankan query dan mengambil hasil

$data = [];
while ($row = $result->fetch_assoc()) {
    // Jika gambar tersedia, buat URL gambar dengan base_url
    if (!empty($row['gambar'])) {
        $row['gambar'] = $base_url . ltrim($row['gambar'], '/'); 
    }
    $data[] = $row; // Menyimpan data ke dalam array
}

// Menutup statement untuk membebaskan sumber daya
$stmt->close();

// Struktur respons JSON dengan pagination
if (empty($data)) {
    // Jika tidak ada data, kirimkan pesan error dengan kode 404
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
    // Jika data ditemukan, kirimkan pesan sukses dengan data dan informasi pagination
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
