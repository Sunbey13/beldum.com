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
    <title>Лист заданий № <?php echo $_GET['id'] ?></title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="task-list">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Задания листа № <?php echo $_GET['id'] ?></h5>
        </div>
        <div class="list-group">
            <div class="list-group-item list-group-item-action" aria-current="true" v-show="!task_list" v-cloak><div class="w-100 text-center"><h5 class="mb-1">Загрузка заданий...</h5></div></div>
            <div class="list-group-item" aria-current="true" v-for="task in task_list" v-cloak>
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ task.organ_name }}</h5>
                    <small v-if="task.state == 1">Статус: в работе</small>
                    <small v-else-if="task.state == 2">Статус: завершено</small>
                </div>
                <p class="mb-1"><b>Адрес:</b> {{ task.organ_address }}</p>
                <p class="mb-1"><b>Задача:</b> {{ task.task_type }}</p>
                <p class="mb-1"><b>Груз:</b> {{ task.cargo_name }}</p>
                <p class="mb-1"><b>Контактное лицо на фирме:</b> {{ task.curator_organ }}, {{ task.curator_organ_tel }}</p>
                <p class="mb-1"><b>Контактное лицо "заказчика":</b> {{ task.curator_remeza }}</p>
                <p class="mb-1"><b>Получатель:</b> {{ task.name }}</p>
                <button v-if="task.state == 1" class="btn btn-sm btn-primary w-100" @click="setAsDone(task.task_id)">Отметить выполненным</button>
                <button v-else-if="task.state == 2" class="btn btn-sm btn-secondary w-100" @click="returnToWork(task.task_id)">Вернуть в работу</button>
            </div>
        </div>
    </div>

<div v-show="errorMessage">{{errorMessage}}</div>
<div v-show="successMessage">{{successMessage}}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/task-list.js"></script>
<?php include ('../footer.php')?>
</body>
</html>
