<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lokapustaka/config/config.php";

// Konfigurasi Database
$host = DB_HOST;    // Alamat server database
$username = DB_USER;     // Username MySQL
$password = DB_PASS;         // Password MySQL
$dbname = DB_NAME; // Nama database

// Membuat Koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

session_start();