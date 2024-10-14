<?php
session_start();
require 'database.php'; // Pastikan file koneksi database

$alert = ''; // Variabel untuk menyimpan pesan alert

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    // Periksa apakah username atau email sudah ada
    $checkUserStmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $checkUserStmt->bindParam(':username', $username);
    $checkUserStmt->bindParam(':email', $email);
    $checkUserStmt->execute();

    if ($checkUserStmt->rowCount() > 0) {
        // Jika username atau email sudah ada, buat alert
        $alert = "Username atau email sudah ada, silakan gunakan yang lain.";
    } else {
        // Lanjutkan pendaftaran jika username dan email tidak ada
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            // Redirect ke halaman login setelah berhasil
            header("Location: login.php");
            exit;
        } else {
            $alert = "Terjadi kesalahan saat mendaftar, coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS untuk desain halaman register */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"], input[type="password"], input[type="email"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #45a049;
}

.alert {
    padding: 15px;
    background-color: #f44336;
    color: white;
    margin-bottom: 20px;
    border-radius: 4px;
    display: none; /* Disembunyikan secara default */
}

.alert.success {
    background-color: #4CAF50;
}

    </style>
</head>
<body>

    <div class="container">
        <h2>Register</h2>
        
        <!-- Alert untuk pesan error -->
        <?php if ($alert): ?>
        <div class="alert">
            <?php echo $alert; ?>
        </div>
        <?php endif; ?>

        <form action="register.php" method="post" id="registerForm">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>
            <button type="submit">Daftar</button>
        </form>
    </div>

    <!-- JavaScript untuk menampilkan alert -->
    <script>
        // Jika ada alert, tampilkan dengan animasi
        var alertBox = document.querySelector('.alert');
        if (alertBox && alertBox.innerHTML.trim() !== '') {
            alertBox.style.display = 'block';
        }
    </script>
    
</body>
</html>
