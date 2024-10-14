<?php
session_start();
require 'database.php'; // Pastikan file ini terhubung ke database Anda

// Ambil semua album dari database
$stmt = $conn->prepare("SELECT * FROM album");
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Album</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-4">Daftar Album</h1>
    <a href="add_album.php" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Tambah Album Baru</a>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($albums as $album): ?>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($album['NamaAlbum']) ?></h2>
                <p class="mb-2"><?= htmlspecialchars($album['Deskripsi']) ?></p>
                <p class="text-gray-600">Tanggal Dibuat: <?= htmlspecialchars($album['TanggalDibuat']) ?></p>
                <a href="view_photos.php?album_id=<?= $album['AlbumID'] ?>" class="bg-blue-500 text-white px-4 py-2 rounded mt-2 inline-block">Lihat Foto</a>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="gallery.php">Kembali</a>
</div>

</body>
</html>
~