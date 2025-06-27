<?php
session_start();
include 'koneksi.php'; // Pastikan file konfigurasi database tersedia
include 'logoTitle.php';

if (!isset($_SESSION['from_home']) || $_SESSION['from_home'] !== true) {
    header("Location: home.php");
    exit();
}
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        unset($_SESSION['from_home']);
        $_SESSION['authorized'] = true;
        $_SESSION['username'] = $username;
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Login Berhasil!',
                        text: 'Selamat datang, $username',
                        icon: 'success',
                        timer: 1500,
                        timerProgressBar: true,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'home.php';
                    });
                }, 100);
              </script>";
    } else {
        echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Login Gagal!',
                        text: 'Username atau password salah.',
                        icon: 'error',
                        timer: 3000,
                        timerProgressBar: true,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'login.php'; // Redirect setelah login gagal
                    });
                }, 100);
              </script>";
    }
    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.2" />
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            font-family: "Times New Roman", Times, serif;
            background: url('assets/pa.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            padding: 40px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: "Times New Roman", Times, serif;
        }

        .btn {
            font-family: "Times New Roman", Times, serif;
            background-color: green;
            color: white;
            border: none;
            padding: 10px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        .btn:hover {
            background-color: #808080;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
