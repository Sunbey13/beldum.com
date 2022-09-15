<?php
    session_start();
    include('../dbconn.php');
    include('../access/bmtsaccess.php');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../img/BD.ico">
    <title>История | BelDum</title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="history">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">История</h5>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">№ листа</th>
                    <th class="text-center">Дата</th>
                    <th class="text-center">Водитель</th>
                    <th class="text-center">Маршрут</th>
                    <th class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
            <tr v-if="!task_lists && !emptyTableMessage">
                <td class="text-center" colspan="5">Загрузка листов заданий...</td>
            </tr>
            <tr v-else-if="emptyTableMessage" v-cloak>
                <td class="text-center" colspan="5">{{emptyTableMessage}}</td>
            </tr>
            <tr v-for="row in task_lists" v-cloak>
                <td class="text-center">{{row.task_list_id}}</td>
                <td class="text-center">{{row.date}}</td>
                <td class="text-center">{{row.initials}}</td>
                <td class="text-center">{{row.route}}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Действия
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a :href="'task-list.php?id='+row.task_list_id+'&action=1'" target="_blank" class="dropdown-item" @click="printTaskList(row.task_list_id)">Сохранить в PDF</a></li>
                            <li><a :href="'task-list.php?id='+row.task_list_id+'&action=0'" target="_blank" class="dropdown-item" @click="printTaskList(row.task_list_id)">Посмотреть PDF</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div v-show="successMessage" v-cloak>{{ successMessage }}</div>
    <div v-show="errorMessage" v-cloak>{{ errorMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/history.js"></script>
<?php include ('../footer.php')?>
</body>
</html>
