<?php
    session_start();
    include('../dbconn.php');
    include('../access/customeraccess.php');
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
<nav class="navbar navbar-expand-lg navbar-dark bg-lochinvar mb-3">
    <div class="container">
        <a class="navbar-brand" href="main.php">
            <img src="../img/logo.svg" height="31" width="141" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarText">
            <form class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Выйти</a>
                    </li>
                </ul>
            </form>
        </div>
    </div>
</nav>
<div class="container" id="main-customer">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" v-cloak>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ modal.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearFormData"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Вид перевозки</h6>
                            <select class="form-control" v-model="task.task_type">
                                <option value="" disabled>Выберите вид перевозки</option>
                                <option value="Привезти">Привезти</option>
                                <option value="Отвезти">Отвезти</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h6>Дата</h6>
                            <input type="date" class="form-control" v-model="task.date">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Название груза</h6>
                            <input type="text" class="form-control" placeholder="Введите название груза" v-model="task.cargo_name" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <h6>Вес груза</h6>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control" placeholder="Введите вес груза" v-model="task.cargo_weight">
                                <span class="input-group-text" id="weight">, кг.</span>
                            </div>
                        </div>
                    </div>
                    <h6>Габариты груза</h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Д</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Длина груза (cм)" v-model="task.cargo_l">
                        <span class="input-group-text">Ш</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Ширина груза (cм)" v-model="task.cargo_w">
                        <span class="input-group-text">В</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Высота груза (cм)" v-model="task.cargo_h">
                    </div>
                    <h6>Организация</h6>
                    <div class="input-group mb-3">
                        <select class="form-control" v-model="task.organ_id">
                            <option value="" disabled>Выберите организацию</option>
                            <option :value="row.organ_id" v-for="row in organs">{{ row.organ_name }}</option>
                        </select>
                        <select class="form-control" v-model="task.organ_address" :disabled="!(task.organ_id)">
                            <option value="" disabled>Выберите адрес</option>
                            <template v-for="row in organs" v-if="row.organ_id == task.organ_id">
                                <option v-for="address in row.organ_address" :value="address.region + ' ' + address.city + ' ' + address.street">{{ address.region }} {{ address.city }} {{ address.street }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Куратор от организации</h6>
                            <input v-model="task.curator_organ" class="form-control" type="text" placeholder="Введите куратора от организации" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <h6>Контакты куратора от организации</h6>
                            <input v-model="task.curator_organ_tel" class="form-control" type="text" placeholder="Введите контактную информацию" maxlength="30">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Куратор от ЗАО "Ремеза"</h6>
                            <input v-model="task.curator_remeza" type="text" class="form-control" placeholder='Введите куратора от ЗАО "Ремеза"' maxlength="30">
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control task-notes" placeholder="Примечания..." v-model="task.notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter">
                </div>
            </div>
        </div>
    </div>

    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Заявки</h5>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Создать заявку</button>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="text-center">Дата</th>
                <th class="text-center">Вид перевозки</th>
                <th class="text-center">Организация</th>
                <th class="text-center">Груз</th>
                <th class="text-center">Исполнитель</th>
                <th class="text-center">Статус</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="!tasks && !emptyRequestsMessage">
                <td class="text-center" colspan="7">Загрузка заявок...</td>
            </tr>
            <tr v-else-if="emptyRequestsMessage">
                <td class="text-center" colspan="7" v-cloak>{{emptyRequestsMessage}}</td>
            </tr>
            <tr v-for="row in tasks">
                <td class="text-center">{{ row.date }}</td>
                <td class="text-center">{{ row.task_type }}</td>
                <td class="text-center">{{ row.organ_name }}</td>
                <td class="text-center">{{ row.cargo_name }}</td>
                <td class="text-center" v-if="row.driver">{{ row.driver }}</td>
                <td class="text-center" v-else><h5><span class="badge bg-danger">Не назначен</span></h5></td>
                <td class="text-center">
                    <h5><span v-if="row.state == '0'" class="badge bg-success status-label">Новая</span>
                    <span v-else-if="row.state == '1'" class="badge bg-primary status-label">В работе</span>
                    <span v-else class="badge bg-secondary status-label">Завершена</span></h5>
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <button :disabled="row.state != 0" class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Действия
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><button class="dropdown-item" @click="getTask(row.task_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</button>
                            <li><button class="dropdown-item" @click="deleteTask(row.task_id)">Удалить</button></li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div v-show="successMessage">{{ successMessage }}</div>
    <div v-show="errorMessage">{{ errorMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/main.js"></script>
<?php include ('../footer.php')?>
</body>
</html>