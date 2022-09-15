<?php
    session_start();
    include('dbconn.php');

    if(!isset($_SESSION['role'])||(trim($_SESSION['role']) == '')) {
        header('location: index.php');
    }
?>
<!doctype html>
<html lang="ru" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/BD.ico">
    <title>Доступ запрещен | BelDum</title>
</head>
<body class="h-75">
<nav class="navbar navbar-expand-lg navbar-dark bg-lochinvar mb-3">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.svg" height="31" width="141" alt="logo">
        </a>
    </div>
</nav>
<div class="container h-100 text-center">
    <div class="d-flex align-items-center h-100">
        <h1 class="d-inline-block w-100">Ошибка 403. Доступ запрещен!</h1>
    </div>
</div>
</body>
<footer class="text-center text-lg-start">
    <!-- Copyright -->
    <div class="text-center p-3">
        © 2022 BSUIR:
        <a class="text-dark" href="#">Белоусов Дмитрий</a>
    </div>
    <!-- Copyright -->
</footer>
</html>