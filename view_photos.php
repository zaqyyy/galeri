<?php
session_start();
require 'database.php'; // Pastikan file ini terhubung ke database Anda

if (!isset($_GET['album_id'])) {
    header("Location: gallery.php");
    exit;
}

$album_id = $_GET['album_id'];

// Ambil foto dari database berdasarkan album_id
$stmt = $conn->prepare("SELECT * FROM photos WHERE album_id = :album_id"); // Pastikan ada tabel 'photos'
$stmt->bindParam(':album_id', $album_id);
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil nama album untuk ditampilkan
$album_stmt = $conn->prepare("SELECT NamaAlbum FROM album WHERE AlbumID = :album_id");
$album_stmt->bindParam(':album_id', $album_id);
$album_stmt->execute();
$album = $album_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($album['NamaAlbum']) ?> - Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($album['NamaAlbum']) ?></h1>
    <a href="gallery.php" class="bg-gray-500 text-white px-4 py-2 rounded mb-4 inline-block">Kembali ke Daftar Album</a>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($photos as $photo): ?>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <img src="<?= htmlspecialchars($photo['photo_url']) ?>" alt="" class="w-full h-48 object-cover rounded">
                <p class="text-gray-600"><?= htmlspecialchars($photo['description']) ?></p>
            </div>
        <?php endforeach; ?>

        <?php if (empty($photos)): ?>
            <p class="text-gray-600">Tidak ada foto dalam album ini.</p>
        <?php endif; ?>
    </div>
    <a href="galer.php">Kembali</a>
</div>

</body>
</html>
