<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $photo_id = $_POST['photo_id'];

    // Prepare the SQL statement to delete the photo
    $stmt = $conn->prepare("DELETE FROM photos WHERE photo_id = :photo_id");
    $stmt->bindParam(':photo_id', $photo_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error deleting photo.";
    }
}
?>
