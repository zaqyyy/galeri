<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo = $_FILES['photo'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Check if the uploads folder exists
    $target_dir = "uploads/";
    
    // Ensure the folder exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Create the folder if it doesn't exist
    }

    $photo_url = basename($photo["name"]);
    $target_file = $target_dir . $photo_url;

    if (move_uploaded_file($photo["tmp_name"], $target_file)) {
        // Insert the photo details into the database
        $stmt = $conn->prepare("INSERT INTO photos (user_id, photo_url, description) VALUES (:user_id, :photo_url, :description)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':photo_url', $photo_url);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        header("Location: gallery.php");
        exit;
    } else {
        echo "Failed to upload photo.";
    }
}
?>

