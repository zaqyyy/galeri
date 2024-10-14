<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'database.php'; // Pastikan file ini terhubung ke database Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaAlbum = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];
    $tanggalDibuat = $_POST['tanggal_dibuat'];
    $user_id = $_SESSION['user_id'];

    // Persiapkan dan eksekusi query untuk menambahkan album
    $stmt = $conn->prepare("INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, user_id) VALUES (:nama_album, :deskripsi, :tanggal_dibuat, :user_id)");
    $stmt->bindParam(':nama_album', $namaAlbum);
    $stmt->bindParam(':deskripsi', $deskripsi);
    $stmt->bindParam(':tanggal_dibuat', $tanggalDibuat);
    $stmt->bindParam(':user_id', $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Album berhasil ditambahkan!'); window.location.href = 'gallery.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan album. Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Album</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-4">Tambah Album Baru</h1>

    <form method="POST" action="" class="bg-white p-4 rounded-lg shadow-md">
        <div class="mb-4">
            <label for="nama_album" class="block text-lg font-medium mb-2">Nama Album:</label>
            <input type="text" name="nama_album" id="nama_album" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-lg font-medium mb-2">Deskripsi:</label>
            <textarea name="deskripsi" id="deskripsi" class="w-full p-2 border rounded" rows="4"></textarea>
        </div>
        <div class="mb-4">
            <label for="tanggal_dibuat" class="block text-lg font-medium mb-2">Tanggal Dibuat:</label>
            <input type="date" name="tanggal_dibuat" id="tanggal_dibuat" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Album</button>
    </form>
    <a href="gallery.php">Kembali</a>
</div>

</body>
</html>
