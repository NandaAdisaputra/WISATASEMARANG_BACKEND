<?php
// Mengimpor file konfigurasi database
include 'config.php';

// Mengatur header untuk mengembalikan respons dalam format JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Mengizinkan akses API dari semua domain

// Memeriksa apakah parameter 'id' telah diberikan dalam URL
if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "ID wisata diperlukan"]);
    exit; // Menghentikan eksekusi script jika ID tidak diberikan
}

// Mengamankan input ID dengan mengonversinya menjadi integer
$id = intval($_GET['id']);

// Menyiapkan query SQL untuk mengambil data wisata berdasarkan ID
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata WHERE id = ?");
$stmt->bind_param("i", $id); // Mengikat parameter ID sebagai integer
$stmt->execute(); // Menjalankan query
$result = $stmt->get_result(); // Mendapatkan hasil query

// Memeriksa apakah ada hasil yang ditemukan
if ($result->num_rows > 0) {
    $wisata = $result->fetch_assoc(); // Mengambil data dalam bentuk array asosiatif

    // Jika ada gambar, tambahkan URL lengkap berdasarkan alamat server
    if (!empty($wisata['gambar'])) {
        $wisata['gambar'] = "http://" . $_SERVER['SERVER_ADDR'] . "/" . ltrim($wisata['gambar'], '/');
    }

    // Mengembalikan data dalam format JSON dengan status sukses
    echo json_encode(["status" => "success", "data" => $wisata], JSON_PRETTY_PRINT);
} else {
    // Jika data tidak ditemukan, kirim pesan error dalam format JSON
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
}

// Menutup statement untuk membebaskan sumber daya
$stmt->close();
?>
