<?php
    session_start();
    include('access/loginredirect.php');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/x-icon" href="img/BD.ico">
    <title>Войти | BelDum</title>
</head>
<body class="text-center login-body">
<main class="form-signin" id="login-form">
    <form>
        <img class="mb-3" src="img/logo.svg" height="48" width="216" alt="logo">
        <div class="form-floating">
            <input class="form-control" type="text" v-model="login" id="login" v-on:keyup="keymonitor" placeholder="Введите логин" autocomplete="off">
            <label for="login">Логин</label>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" type="password" v-model="password" id="password" placeholder="Введите пароль" v-on:keyup="keymonitor" autocomplete="off">
            <label for="password">Пароль</label>
        </div>
        <input class="w-100 btn btn-lg btn-primary" type="button" v-model="actionButton" @click="checkLogin">
        <div v-if="successMessage" class="alert alert-success text-center mt-3" role="alert" v-cloak>
            <b>{{ successMessage }}</b>
        </div>
        <div v-if="errorMessage" class="alert alert-danger text-center mt-3" role="alert" v-cloak>
            <b>{{ errorMessage }}</b>
        </div>
    </form>
</main>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/login.js"></script>
</body>
</html>