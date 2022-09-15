<?php
    session_start();
    include('../dbconn.php');
    include('../access/bmtsaccess.php');
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
    <title>Авто | BelDum</title>
</head>
<body>
<?php include ('nav.php') ?>
<div class="container" id="cars">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Авто</h5>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Добавить авто</button>
        </div>
        <table class="table table-bordered" v-cloak>
            <thead>
            <tr>
                <th class="text-center">Марка</th>
                <th class="text-center">Модель</th>
                <th class="text-center">Номер</th>
                <th class="text-center">Владелец</th>
                <th class="text-center">Длина, см</th>
                <th class="text-center">Ширина, см</th>
                <th class="text-center">Высота, см</th>
                <th class="text-center">Грузоподъемность, кг</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="!cars && !emptyTableMessage"><td class="text-center" colspan="9">Загрузка авто...</td></tr>
            <tr v-else-if="emptyTableMessage"><td class="text-center" colspan="9" v-cloak>{{emptyTableMessage}}</td></tr>
            <tr v-for="car in cars" v-cloak>
                <td class="text-center">{{ car.mark }}</td>
                <td class="text-center">{{ car.model }}</td>
                <td class="text-center">{{ car.number }}</td>
                <td class="text-center">{{ car.surname + " " + car.name + " " + car.last_name}}</td>
                <td class="text-center">{{ car.carcass_l }}</td>
                <td class="text-center">{{ car.carcass_w }}</td>
                <td class="text-center">{{ car.carcass_h }}</td>
                <td class="text-center">{{ car.carcass_weight }}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Действия
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><button class="dropdown-item" role="button" @click="getCar(car.car_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</button></li>
                            <li><button class="dropdown-item" role="button" @click="deleteCar(car.car_id)">Удалить</button></li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" v-cloak>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ modal.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearFormData"></button>
                </div>
                <div class="modal-body">
                    <h6>Данные автомобиля</h6>
                    <input class="form-control mb-3" type="text" placeholder="Введите марку авто"  v-model="car.mark" maxlength="30">
                    <input class="form-control mb-3" type="text" placeholder="Введите модель авто" v-model="car.model" maxlength="30">
                    <input class="form-control mb-3" type="text" placeholder="Введите регистрационный номер" v-model="car.number" maxlength="10">
                    <h6>Характеристики автомобиля</h6>
                    <div class="input-group mb-3">
                        <input type="number" step="0.1" class="form-control" aria-describedby="weight" placeholder="Введите грузоподъемность" v-model="car.carcassWeight">
                        <span class="input-group-text" id="weight">, кг.</span>
                    </div>
                    <h6>Кузов</h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Д</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Длина (см)" v-model="car.carcassL">
                        <span class="input-group-text">Ш</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Ширина (см)" v-model="car.carcassW">
                        <span class="input-group-text">В</span>
                        <input class="form-control" type="number" step="0.1" placeholder="Высота (см)" v-model="car.carcassH">
                    </div>
                    <h6>Владелец(водитель) автомобиля</h6>
                    <select class="form-control" v-model="car.driverID">
                        <option value="" disabled>Выберите владельца (водителя)</option>
                        <option v-for="row in drivers" v-bind:value="row.driver_id">{{ row.surname + " " + row.name + " " + row.last_name }}</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter" v-bind:disabled="!(car.mark != '' && car.model != '' && car.number != '' && car.driverID != '')">
                </div>
            </div>
        </div>
    </div>

    <div v-show="successMessage" v-cloak>{{ successMessage }}</div>
    <div v-show="errorMessage" v-cloak>{{ errorMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/cars.js"></script>
<?php include ('../footer.php')?>
</body>
</html>