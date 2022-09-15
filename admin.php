<?php
    session_start();
    include('dbconn.php');
    if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
        header('location:index.php');
    }
    if ($_SESSION['role'] != 0) {
        header('location:access-denied.php');
    }
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/BD.ico">
    <title>Панель управления | BelDum</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-lochinvar mb-3">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../img/logo.svg" height="31" width="141" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarText">
            <form class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Выйти</a>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</nav>
<div class="container" id="admin">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" v-cloak>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ modal.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearFormData"></button>
                </div>
                <div class="modal-body">
                    <h6>Права доступа</h6>
                    <select class="form-control mb-3" v-model="user.role" required>
                        <option disabled value="">Выберите права доступа</option>
                        <option value="1">Начальник БМТС</option>
                        <option value="2">Инженер БМТС</option>
                        <option value="3">Водитель</option>
                        <option value="4">Сотрудник подразделения</option>
                    </select>
                    <h6>{{ modal.subtitle1 }}</h6>
                    <template v-if="user.role < 3">
                        <input class="form-control mb-3" type="text" v-model="user.surname" placeholder="Введите фамилию" required maxlength="30">
                        <input class="form-control mb-3" type="text" v-model="user.name" placeholder="Введите имя" required maxlength="30">
                        <input class="form-control mb-3" type="text" v-model="user.last_name" placeholder="Введите отчество" required maxlength="30">
                    </template>
                    <input class="form-control mb-3" v-else-if="user.role == 4" type="text" v-model="user.name" placeholder="Введите название подразделения" required>
                    <select class="form-control mb-3" v-else-if="user.role == 3" v-model="driver">
                        <option value="" disabled>Выберите водителя</option>
                        <option v-for="row in drivers" :value="row">{{ row.surname }} {{ row.name }} {{ row.last_name }}</option>
                    </select>
                    <h6>{{ modal.subtitle2 }}</h6>
                    <input class="form-control mb-3" type="text" v-model="user.login" placeholder="Введите логин" maxlength="30">
                    <input class="form-control mb-3" v-if="modal.action == 'add'" type="password" v-model="user.password1" placeholder="Введите пароль" maxlength="30">
                    <input class="form-control mb-3" v-if="modal.action == 'add'" type="password" v-model="user.password2" placeholder="Повторите пароль" maxlength="30">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter"  v-bind:disabled="!((user.role < 3 && user.name != '' && user.surname != '' && user.login != '' && ((user.password1 != '' && user.password2 != '') || modal.action == 'update')) || (user.role == 3 && user.login != '' && ((user.password1 != '' && user.password2 != '') || modal.action == 'update') && driver != '') || (user.role == 4 && user.name != '' && user.login != '' && ((user.password1 != '' && user.password2 != '') || modal.action == 'update')))">
                </div>
            </div>
        </div>
    </div>

    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Пользователи</h5>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Добавить пользователя</button>
        </div>
        <table class="table table-bordered" v-cloak>
            <thead>
            <tr>
                <th class="text-center">id</th>
                <th class="text-center">Фамилия</th>
                <th class="text-center">Имя</th>
                <th class="text-center">Отчество</th>
                <th class="text-center">Логин</th>
                <th class="text-center">Права доступа</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="!users && !emptyTableMessage">
                <td class="text-center" colspan="7">Загрузка пользователей...</td>
            </tr>
            <tr v-else-if="emptyTableMessage" v-cloak>
                <td class="text-center" colspan="7">{{emptyTableMessage}}</td>
            </tr>
            <tr v-for="row in users" v-if="row.role != 'Администратор ИС'">
                <td class="text-center">{{ row.user_id }}</td>
                <td class="text-center">{{ row.surname }}</td>
                <td class="text-center">{{ row.name }}</td>
                <td class="text-center">{{ row.last_name }}</td>
                <td class="text-center">{{ row.login }}</td>
                <td class="text-center">{{ row.role }}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Действия
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><button type="button" class="dropdown-item" @click="getUser(row.user_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</button></li>
                            <li><button type="button" class="dropdown-item" @click="deleteUser(row.user_id)">Удалить</button></li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div v-if="successMessage" v-cloak>{{ successMessage }}</div>
    <div v-if="errorMessage" v-cloak>{{ errorMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/admin.js"></script>
<?php include ('footer.php')?>
</body>
</html>
