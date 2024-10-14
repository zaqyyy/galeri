<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'database.php';

$photo_id = $_GET['photo_id'] ?? null;
if (!$photo_id) {
    header("Location: gallery.php");
    exit;
}

$photo_id = intval($photo_id);
$user_id = $_SESSION['user_id'];

// Fetch photo details along with the user who posted it
$stmt = $conn->prepare("SELECT p.*, u.username FROM photos p JOIN users u ON p.user_id = u.user_id WHERE p.photo_id = :photo_id");
$stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
$stmt->execute();
$photo = $stmt->fetch(PDO::FETCH_ASSOC);

// Menghitung jumlah like untuk foto
$stmt = $conn->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE photo_id = :photo_id");
$stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
$stmt->execute();
$totalLikes = $stmt->fetch(PDO::FETCH_ASSOC)['total_likes'];

if (!$photo) {
    header("Location: gallery.php");
    exit;
}

// Check if the user has already liked the photo
$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = :user_id AND photo_id = :photo_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
$stmt->execute();
$userLiked = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle like/unlike actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'like') {
        $stmt = $conn->prepare("INSERT INTO likes (user_id, photo_id) VALUES (:user_id, :photo_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
        $stmt->execute();
    } elseif ($_POST['action'] == 'unlike') {
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = :user_id AND photo_id = :photo_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    header("Location: detail.php?photo_id=" . $photo_id);
    exit;
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_text'])) {
    $comment = $_POST['comment_text'];
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO comments (photo_id, user_id, comment_text, created_at) VALUES (:photo_id, :user_id, :comment_text, :created_at)");
    $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':comment_text', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $stmt->execute();
}

// Fetch all comments for the current photo
$comments_stmt = $conn->prepare("SELECT c.comment_text, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.photo_id = :photo_id ORDER BY c.created_at DESC");
$comments_stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
$comments_stmt->execute();
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle add to album submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_album'])) {
    $album_id = intval($_POST['album_id']);

    // Masukkan data ke tabel photos untuk mengaitkan foto ke album
    $stmt = $conn->prepare("UPDATE photos SET album_id = :album_id WHERE photo_id = :photo_id");
    $stmt->bindParam(':album_id', $album_id, PDO::PARAM_INT);
    $stmt->bindParam(':photo_id', $photo_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect untuk mencegah pengiriman ulang formulir
    header("Location: detail.php?photo_id=" . $photo_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-4">Detail Foto</h1>

    <img src="uploads/<?php echo $photo['photo_url']; ?>" alt="photo" class="w-full h-auto mb-4 rounded-lg shadow-md">
    
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <p class="text-lg"><?php echo htmlspecialchars($photo['description']); ?></p>
        <p class="mt-2 text-gray-600">Diposting oleh: <strong><?php echo htmlspecialchars($photo['username']); ?></strong></p>
        
        <!-- Tampilkan tanggal foto diunggah -->
        <p class="mt-2 text-gray-600">Diposting pada: <strong><?php echo date('F j, Y, g:i a', strtotime($photo['uploaded_at'])); ?></strong></p>
    </div>

    <!-- Form untuk memperbarui foto -->
    <h2 class="text-xl font-semibold mb-2">Perbarui Foto</h2>
    <form method="POST" action="edit.php" class="mb-4">
        <input type="hidden" name="photo_id" value="<?php echo $photo['photo_id']; ?>">
        <textarea name="description" class="w-full p-2 border rounded" placeholder="Perbarui deskripsi..." required><?php echo htmlspecialchars($photo['description']); ?></textarea>
        <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Perbarui Foto</button>
    </form>

    <!-- Form untuk menambahkan foto ke album -->
    <h2 class="text-xl font-semibold mb-2">Tambah Foto ke Album</h2>
    <form method="POST" action="detail.php?photo_id=<?php echo $photo_id; ?>" class="mb-4">
        <label for="album" class="block text-lg font-medium mb-2">Pilih Album:</label>
        <select name="album_id" id="album" class="w-full p-2 border rounded mb-2" required>
            <option value="" disabled selected>Pilih album</option>
            <?php
            // Ambil daftar album yang dibuat oleh pengguna
            $album_stmt = $conn->prepare("SELECT AlbumID, NamaAlbum FROM album WHERE user_id = :user_id");
            $album_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $album_stmt->execute();
            $albums = $album_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($albums as $album) {
                echo "<option value=\"{$album['AlbumID']}\">" . htmlspecialchars($album['NamaAlbum']) . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="add_to_album" class="mt-2 bg-green-500 text-white px-4 py-2 rounded">Tambah ke Album</button>
    </form>

    <!-- Like/Unlike Button -->
    <div class="mb-4 text-center">
        <form method="POST" class="inline-block">
            <?php if ($userLiked): ?>
                <input type="hidden" name="action" value="unlike">
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-full shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-300" aria-label="Unlike">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656l-6.36 6.36a.5.5 0 01-.707 0l-6.36-6.36a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    Batal Like (<?php echo $totalLikes; ?>)
                </button>
            <?php else: ?>
                <input type="hidden" name="action" value="like">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-300" aria-label="Like">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656l-6.36 6.36a.5.5 0 01-.707 0l-6.36-6.36a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    Suka (<?php echo $totalLikes; ?>)
                </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Comments Section -->
    <h2 class="text-xl font-semibold mb-2">Komentar</h2>
    <form method="POST" action="detail.php?photo_id=<?php echo $photo_id; ?>" class="mb-4">
        <textarea name="comment_text" class="w-full p-2 border rounded" placeholder="Tinggalkan komentar..." required></textarea>
        <button type="submit" class="mt-2 bg-purple-500 text-white px-4 py-2 rounded">Kirim Komentar</button>
    </form>

    <button class="bg-green-500 text-white rounded-full px-4 py-1">
    <a href="download.php?image=<?php echo urlencode($photo['photo_url']); ?>" class="text-white">
        Unduh
    </a>
</button>


    <div class="comments bg-white p-4 rounded-lg shadow-md">
        <?php foreach ($comments as $comment): ?>
            <div class="comment mb-2">
                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>: 
                <span><?php echo htmlspecialchars($comment['comment_text']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="gallery.php">Kembali</a>
</div>

</body>
</html>
