<?php
    if(isset($_SESSION['role']) && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2)) {
        header('location:bmts/main.php');
    } else if (isset($_SESSION['role']) && $_SESSION['role'] == 3) {
        header('location:driver/main.php');
    } else if (isset($_SESSION['role']) && $_SESSION['role'] == 4) {
        header('location:customer/main.php');
    } else if (isset($_SESSION['role']) && $_SESSION['role'] == 0){
        header('location:admin.php');
    }
?>