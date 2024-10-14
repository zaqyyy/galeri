<?php
if (isset($_GET['image'])) {
    $file = 'uploads/' . urldecode($_GET['image']); // Define the path to the image
    
    if (file_exists($file)) {
        // Set headers to initiate the file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        // If the file doesn't exist, redirect back or show an error
        echo "File tidak ditemukan.";
    }
} else {
    // If no image is specified, redirect back or show an error
    echo "Tidak ada file yang dipilih untuk diunduh.";
}
