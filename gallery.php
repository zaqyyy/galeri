<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'database.php';

// Initialize search variable
$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Fetch all photos with search functionality
$photos = $conn->prepare("SELECT * FROM photos WHERE description LIKE :search");
$photos->execute(['search' => '%' . $search . '%']);
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
        background: linear-gradient(135deg, #74ebd5, #9face6);
        padding: 20px;
        color: #333;
        overflow-x: hidden;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(0, 123, 255, 0.8);
        padding: 15px 30px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .navbar a {
        color: #ecf0f1;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.3s;
    }

    .navbar a:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    h1 {
        text-align: center;
        color: #fff;
        margin-bottom: 20px;
        font-size: 36px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .gallery-container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        transition: transform 0.3s;
    }

    .gallery-container:hover {
        transform: translateY(-2px);
    }

    h2 {
        margin-bottom: 20px;
        text-align: center;
        color: #007bff;
        font-size: 28px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
    }

    input[type="file"],
    input[type="text"] {
        width: 100%;
        max-width: 400px;
        padding: 12px;
        margin-bottom: 15px;
        border: 2px solid #007bff;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="file"]:focus,
    input[type="text"]:focus {
        border-color: #0056b3;
        outline: none;
    }

    button {
        width: 160px;
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    button:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding-top: 20px;
    }

    .photo-item {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        text-align: center;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        padding-bottom: 10px;
    }

    .photo-item img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .photo-item:hover img {
        transform: scale(1.1);
    }

    .photo-item:hover {
        transform: translateY(-10px);
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
        color: #555;
        font-size: 14px;
        padding: 0 10px;
    }

    .footer {
        text-align: center;
        margin-top: 30px;
        color: #555;
        font-size: 14px;
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }

    .search-bar {
        margin-top: 20px;
    }

    .search-bar input[type="text"] {
        width: 100%;
        max-width: 400px;
        padding: 12px;
        border-radius: 8px;
        border: 2px solid #007bff;
    }

    .search-bar button {
        margin-left: 10px;
    }
</style>

<div class="navbar">
    <div class="navbar-left">
        <a href="gallery.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="album.php">Buat album</a>
        <a href="logout.php">Logout</a>
        <a href="galer.php">Album Koleksi</a>
    </div>
    <div class="navbar-right">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="dashboard.php">Admin Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<div class="gallery-container">
    <h2>Upload Photo</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="photo" required>
        <input type="text" name="description" placeholder="Photo Description" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Search Photos</h2>
    <form method="POST" action="" class="search-bar">
        <input type="text" name="search" placeholder="Search by description" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <h2>All Photos</h2>
    <div class="gallery">
        <?php while($photo = $photos->fetch(PDO::FETCH_ASSOC)): ?>
            <a href="detail.php?photo_id=<?php echo $photo['photo_id']; ?>" class="photo-item">
                <img src="uploads/<?php echo $photo['photo_url']; ?>" alt="photo">
                <p><?php echo $photo['description']; ?></p>
                <?php if ($photo['user_id'] == $_SESSION['user_id']): ?>
                    <!-- You can add delete/edit buttons here -->
                <?php endif; ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> Your Website. All rights reserved.</p>
</div>
