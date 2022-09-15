<?php
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location: ../index.php');
}
if ($_SESSION['role'] != 4) {
    header('location: ../access-denied.php');
}
?>