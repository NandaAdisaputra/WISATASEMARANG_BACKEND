<?php
require_once "db_config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Hapus data
    $sql = "DELETE FROM tempat_wisata WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data berhasil dihapus"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus data"]);
    }
} else {
    echo json_encode(["error" => "Metode request tidak valid"]);
}
?>