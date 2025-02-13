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

// Ambil data dari form
$nama = $_POST['nama'] ?? '';
$lokasi = $_POST['lokasi'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';

// Validasi input
if (empty($nama) || empty($lokasi) || empty($deskripsi)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Semua field harus diisi"]);
    exit;
}

// Cek apakah ada file gambar yang diunggah
if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Gambar tidak ditemukan atau gagal diunggah"]);
    exit;
}

// Konfigurasi upload gambar
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$target_dir = "images/";
$file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

// Validasi ekstensi file
if (!in_array($file_ext, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format gambar harus JPG, JPEG, PNG, atau GIF"]);
    exit;
}

// Gunakan nama unik untuk mencegah duplikasi
$file_name = time() . "_" . uniqid() . "." . $file_ext;
$target_file = $target_dir . $file_name;

// Pindahkan file ke folder server
if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal mengupload gambar"]);
    exit;
}

// Simpan data ke database
$stmt = $conn->prepare("INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $lokasi, $deskripsi, $target_file);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil ditambahkan",
        "data" => [
            "id" => $stmt->insert_id,
            "nama" => $nama,
            "lokasi" => $lokasi,
            "deskripsi" => $deskripsi,
            "gambar" => "http://" . $_SERVER['HTTP_HOST'] . "/" . $target_file
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menambahkan data"]);
}

// Tutup statement
$stmt->close();
?>
