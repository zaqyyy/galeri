<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'database.php';

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all photos uploaded by the user
$photos = $conn->prepare("SELECT * FROM photos WHERE user_id = :user_id");
$photos->bindParam(':user_id', $user_id);
$photos->execute();
?>

<!-- Add CSS for enhanced design -->
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f4f4f9;
        padding: 20px;
    }

    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .user-info {
        text-align: center;
        margin-bottom: 30px;
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .photo-item {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .photo-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .photo-item:hover {
        transform: translateY(-5px);
    }
</style>

<div class="profile-container">
    <h1>User Profile</h1>
    
    <div class="user-info">
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Your Photos</h2>
    <div class="gallery">
        <?php while($photo = $photos->fetch(PDO::FETCH_ASSOC)): ?>
            <a href="detail.php?photo_id=<?php echo $photo['photo_id']; ?>" class="photo-item">
                <img src="uploads/<?php echo $photo['photo_url']; ?>" alt="photo">
                <p><?php echo htmlspecialchars($photo['description']); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>
<a href="gallery.php">kembali</a>