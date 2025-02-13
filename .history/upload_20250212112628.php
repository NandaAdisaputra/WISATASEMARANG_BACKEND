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

    // Cek apakah file gambar diunggah
    if (isset($_FILES['gambar'])) {
        $upload_dir = "uploads/";  // Folder penyimpanan gambar
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Buat folder jika belum ada
        }

        $file_name = basename($_FILES['gambar']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Simpan hanya nama file ke database
            $sql = "INSERT INTO tempat_wisata (nama, lokasi, deskripsi, gambar) 
                    VALUES ('$nama', '$lokasi', '$deskripsi', '$file_name')";
            
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Data berhasil disimpan", "file" => $file_name]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengunggah gambar"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File gambar tidak ditemukan"]);
    }
}

$conn->close();
?>
