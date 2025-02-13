<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "wisata_semarang";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}
?>