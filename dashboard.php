<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'database.php';

// Check user role
if ($_SESSION['role'] === 'user') {
    header("Location: gallery.php");
    exit;
}

// Search feature
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM photos WHERE description LIKE ?");
    $stmt->execute(['%' . $searchQuery . '%']);
    $photos = $stmt;
} else {
    // Fetch all photos if no search is performed
    $photos = $conn->query("SELECT * FROM photos");
}

// Check if the user is an admin
$isAdmin = $_SESSION['role'] === 'admin'; // Assuming 'role' is stored in session
?>

<!-- Add CSS for enhanced design -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #ece9e6 0%, #ffffff 100%);
        padding: 20px;
        color: #333;
        font-size: 16px;
        line-height: 1.6;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 36px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .gallery-container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        transition: transform 0.3s;
    }

    .gallery-container:hover {
        transform: translateY(-5px);
    }

    .nav-links {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }

    .nav-links a {
        padding: 12px 25px;
        background-color: #3498db;
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
        transition: all 0.3s ease;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-links a:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    h2 {
        margin-bottom: 20px;
        text-align: center;
        color: #333;
        font-size: 28px;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    input[type="file"],
    input[type="text"] {
        width: 100%;
        max-width: 400px;
        padding: 12px;
        margin-bottom: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="file"]:focus,
    input[type="text"]:focus {
        border-color: #3498db;
        outline: none;
    }

    button {
        width: 180px;
        padding: 12px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        font-weight: bold;
    }

    button:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding-top: 20px;
    }

    .photo-item {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 15px;
        padding: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .photo-item img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .photo-item:hover img {
        transform: scale(1.05);
    }

    .photo-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    .photo-item button {
        background-color: #e74c3c;
        margin-top: 10px;
        padding: 8px 15px;
        font-size: 14px;
        transition: background-color 0.3s ease;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
    }

    .photo-item button:hover {
        background-color: #c0392b;
    }

    .photo-item p {
        margin-top: 12px;
        color: #777;
        font-size: 14px;
    }

    .back-link {
        display: block;
        margin-top: 20px;
        text-align: center;
        padding: 12px;
        background-color: #2ecc71;
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .back-link:hover {
        background-color: #27ae60;
        transform: scale(1.05);
    }

    .search-container {
        margin-bottom: 20px;
        text-align: center;
    }

    .search-container input[type="text"] {
        padding: 12px;
        width: 50%;
        border-radius: 30px;
        border: 2px solid #ddd;
        transition: border-color 0.3s ease;
        font-size: 16px;
    }

    .search-container button {
        padding: 12px 20px;
        background-color: #3498db;
        color: #fff;
        border-radius: 30px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-container input[type="text"]:focus {
        border-color: #3498db;
    }

    .search-container button:hover {
        background-color: #2980b9;
    }
</style>

<div class="gallery-container">
    <h1>Admin Dashboard</h1>
    
    <div class="nav-links">
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
        <a href="galer.php">Album Koleksi</a>
        <a href="album.php">Album</a>
    </div>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Cari deskripsi foto..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <h2>Upload Photo</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="photo" required>
        <input type="text" name="description" placeholder="Photo Description" required>
        <button type="submit">Upload</button>
    </form>

    <h2>All Photos</h2>
    <div class="gallery">
        <?php if ($photos->rowCount() > 0): ?>
            <?php while($photo = $photos->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="photo-item">
                    <img src="uploads/<?php echo $photo['photo_url']; ?>" alt="photo">
                    <p><?php echo $photo['description']; ?></p>
                    <?php if ($photo['user_id'] == $_SESSION['user_id'] || $isAdmin): ?>
                        <a href="edit.php?photo_id=<?php echo $photo['photo_id']; ?>">Edit</a>
                        <form action="delete.php" method="POST" style="display: inline;">
                            <input type="hidden" name="photo_id" value="<?php echo $photo['photo_id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">No photos found matching your search.</p>
        <?php endif; ?>
    </div>

    <a href="gallery.php" class="back-link">Kembali</a>
</div>
