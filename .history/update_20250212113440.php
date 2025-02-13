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
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];

    // Cek apakah data ada di database
    $sql_check = "SELECT gambar FROM tempat_wisata WHERE id='$id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        $old_image = $row['gambar']; // Nama gambar lama

        $upload_dir = "uploads/";

        if (isset($_FILES['gambar'])) {
            $file_name = basename($_FILES['gambar']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // Hapus gambar lama jika ada
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }

                $sql_update = "UPDATE tempat_wisata SET nama='$nama', lokasi='$lokasi', deskripsi='$deskripsi', gambar='$file_name' WHERE id='$id'";
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mengunggah gambar baru"]);
                exit();
            }
        } else {
            $sql_update = "UPDATE tempat_wisata SET nama='$nama', lokasi='$lokasi', deskripsi='$deskripsi' WHERE id='$id'";
        }

        if ($conn->query($sql_update) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Data berhasil diperbarui"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
    }
}

$conn->close();
?>
