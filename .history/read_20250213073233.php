<?php
include 'config.php';

// Atur header agar output berupa JSON
header("Content-Type: application/json");

// Query untuk mengambil data tempat wisata
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata");
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Pastikan URL gambar sesuai dengan yang tersimpan di database
    $row['gambar'] = "http://localhost/" . $row['gambar']; 
    $data[] = $row;
}

// Tutup statement
$stmt->close();

// Output dalam format JSON
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
