<?php
session_start();
$_SESSION['from_home'] = true; // Set flag sebagai tanda bahwa akses valid
header("Location: data.php");
exit();
?>
