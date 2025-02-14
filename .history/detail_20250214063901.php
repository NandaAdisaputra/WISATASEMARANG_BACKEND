<?php
include 'config.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "ID wisata diperlukan"]);
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT id, nama, lokasi, deskripsi, gambar FROM tempat_wisata WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $wisata = $result->fetch_assoc();
    if (!empty($wisata['gambar'])) {
        $wisata['gambar'] = "http://" . $_SERVER['SERVER_ADDR'] . "/" . ltrim($wisata['gambar'], '/');
    }
    echo json_encode(["status" => "success", "data" => $wisata], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
}

$stmt->close();
?>
