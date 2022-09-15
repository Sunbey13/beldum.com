<?php
    session_start();
    include('../dbconn.php');
    include('../access/driveraccess.php');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../img/BD.ico">
    <title>Главная | BelDum</title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="driver-main">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Листы заданий</h5>
        </div>
        <div class="list-group">
            <div class="list-group-item list-group-item-action" aria-current="true" v-if="!task_lists && !emptyMessage" v-cloak><div class="w-100 text-center"><h5 class="mb-1">Загрузка листов заданий...</h5></div></div>
            <div class="list-group-item list-group-item-action" aria-current="true" v-else-if="emptyMessage" v-cloak><div class="w-100 text-center"><h5 class="mb-1">{{emptyMessage}}</h5></div></div>
            <a class="list-group-item list-group-item-action" aria-current="true" v-for="row in task_lists" :href="'task-list.php?id='+row.task_list_id" v-cloak>
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">Лист заданий № {{ row.task_list_id }}</h5>
                    <p class="mb-1">{{ row.date }}</p>
                </div>
                <p class="mb-1">Маршрут: {{ row.route }}. Заданий: {{ row.tasks.length }} </p>
                <small>Получен от: {{ row.initials }}</small>
            </a>
        </div>
    </div>

    <div v-show="successMessage" v-cloak>{{ successMessage }}</div>
    <div v-show="errorMessage" v-cloak>{{ errorMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/main.js"></script>
<?php include('../footer.php') ?>
</body>
</html>