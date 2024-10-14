<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $photo_id = $_POST['photo_id'];
    $description = $_POST['description'];

    // Prepare the SQL statement to update the photo description
    $stmt = $conn->prepare("UPDATE photos SET description = :description WHERE photo_id = :photo_id");
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':photo_id', $photo_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error updating photo description.";
    }
}

$photo_id = $_GET['photo_id'];
$stmt = $conn->prepare("SELECT * FROM photos WHERE photo_id = :photo_id");
$stmt->bindParam(':photo_id', $photo_id);
$stmt->execute();
$photo = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Photo</title>
</head>
<body>

<h2>Edit Photo Description</h2>

<form method="POST">
    <input type="hidden" name="photo_id" value="<?php echo $photo['photo_id']; ?>">
    <input type="text" name="description" value="<?php echo $photo['description']; ?>" required>
    <button type="submit">Update</button>
</form>

</body>
</html>
