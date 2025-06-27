<?php
session_start();
$_SESSION['from_home_data'] = true;
include 'logoTitle.php';
include 'sesi.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.2" />
    <title>Home</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/home.css">
    
  </head>
  <body>
    <div class="container" id="content">
      <h1>Selamat Datang</h1>
      <button class="btn" onclick="location.href='accessData.php'">Data</button>
      <?php if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) : ?>
        <button class="btn" onclick="location.href='accessLogin.php'">Login</button>
      <?php endif; ?>
      <?php if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === true) : ?>
        <!-- <p class="username">Login sebagai: <?php echo $_SESSION['username']; ?></p> -->
        <button class="btn" onclick="location.href='upload.php'">Upload</button>
        <div class="logout-container">
        <a href="#" onclick="confirmLogout(event)">Logout</a>
        </div>
      <?php endif; ?>
    </div>
    <script>
      function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
          title: "Apakah Anda yakin ingin logout?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Ya, logout!"
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "logout.php";
          }
        });
      }
    </script>
  </body>
</html>
