<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "wisata_semarang";

// Mengaktifkan mode error untuk debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Koneksi ke database
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Set karakter ke UTF-8 untuk mendukung karakter khusus
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Jika koneksi gagal, kirim respons JSON
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal: " . $e->getMessage()]);
    exit;
}
?>
