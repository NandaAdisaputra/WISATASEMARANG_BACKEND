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
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];

    // Cek apakah ada file gambar yang diunggah
    if (isset($_FILES['gambar'])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['gambar']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Simpan data ke database
            $sql_insert = "INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) VALUES ('$nama', '$lokasi', '$deskripsi', '$file_name')";

            if ($conn->query($sql_insert) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Data berhasil ditambahkan"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal menambahkan data: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengunggah gambar"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Gambar harus diunggah"]);
    }
}

$conn->close();
?>
