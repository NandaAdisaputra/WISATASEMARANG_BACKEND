<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "wisata_semarang";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi Gagal: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Cek apakah data ada di database
    $sql_check = "SELECT gambar FROM tempat_wisata WHERE id='$id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $gambar = $row['gambar'];

        // Hapus data dari database
        $sql_delete = "DELETE FROM tempat_wisata WHERE id='$id'";
        if ($conn->query($sql_delete) === TRUE) {
            // Hapus file gambar jika ada
            $upload_dir = "uploads/";
            if ($gambar && file_exists($upload_dir . $gambar)) {
                unlink($upload_dir . $gambar);
            }

            echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus data: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    }
}

$conn->close();
?>
