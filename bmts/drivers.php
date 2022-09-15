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
    <title>Водители | BelDum</title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="drivers">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Водители</h5>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Добавить водителя</button>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="text-center">Фамилия</th>
                <th class="text-center">Имя</th>
                <th class="text-center">Отчество</th>
                <th class="text-center">Телефон</th>
                <th class="text-center">Паспорт</th>
                <th class="text-center">Категории В/У</th>
                <th class="text-center">Дата рождения</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="!drivers && !emptyTableMessage"><td class="text-center" colspan="8">Загрузка водителей...</td></tr>
            <tr v-else-if="emptyTableMessage"><td class="text-center" colspan="8">{{emptyTableMessage}}</td></tr>
            <tr v-for="driver in drivers" v-cloak>
                <td class="text-center">{{ driver.surname }}</td>
                <td class="text-center">{{ driver.name }}</td>
                <td class="text-center">{{ driver.last_name }}</td>
                <td class="text-center">{{ driver.phone }}</td>
                <td class="text-center">{{ driver.passport_series_number }}</td>
                <td class="text-center">{{ driver.licenses }}</td>
                <td class="text-center">{{ driver.date_of_birth }}</td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Действия
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><button class="dropdown-item" @click="getDriver(driver.driver_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</button></li>
                            <li><button class="dropdown-item" @click="deleteDriver(driver.driver_id)">Удалить</button></li>
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
                    <h6>ФИО</h6>
                    <div class="input-group mb-3">
                        <input class="form-control" type="text" v-model="driver.surname" placeholder="Введите фамилию" maxlength="30">
                        <input class="form-control" type="text" v-model="driver.name" placeholder="Введите имя" maxlength="30">
                        <input class="form-control" type="text" v-model="driver.lastName" placeholder="Введите отчество" maxlength="30">
                    </div>
                    <h6>Контактный телефон</h6>
                    <input class="form-control mb-3" type="tel" v-model="driver.phone" placeholder="Введите телефон" maxlength="30">
                    <h6>Паспортные данные</h6>
                    <div class="input-group mb-3">
                        <input class="form-control" type="text" v-model="driver.passport_series_number" placeholder="Введите серию и номер" maxlength="10">
                        <input class="form-control" type="date" v-model="driver.passport_issue_date">
                    </div>
                    <input class="form-control mb-3" type="text" v-model="driver.passport_issued_by" placeholder="Введите орган, которым выдан паспорт" maxlength="150">
                    <h6>Категории вод. удостоверения</h6>
                    <input class="form-control mb-3" type="text" v-model="driver.licenses" placeholder="Введите категории вод. удостоверения" maxlength="10">
                    <h6>Дата рождения</h6>
                    <input class="form-control mb-3" type="date" v-model="driver.dateOfBirth">
                    <h6>Договор</h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text">№</span>
                        <input type="text" class="form-control" placeholder="Введите номер договора" v-model="driver.contract_number" maxlength="15">
                        <span class="input-group-text">от</span>
                        <input type="date" class="form-control" v-model="driver.contract_date">
                    </div>
                    <div class="d-flex align-items-center">
                        <h6 class="w-75">Индивидуальный предприниматель</h6>
                        <input type="text" class="form-control" placeholder="Введите название ИП" v-model="driver.ie_name" maxlength="30">
                    </div>
                    <h6>Платежные данные</h6>
                    <div class="input-group">
                        <span class="input-group-text">IBAN</span>
                        <input type="text" class="form-control input-uppercase" placeholder="Введите IBAN счета" v-model="driver.bank_iban" maxlength="50">
                        <span class="input-group-text">BIK</span>
                        <input type="text" class="form-control input-uppercase" placeholder="Введите BIK банка" v-model="driver.bank_bik" maxlength="15">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter"  v-bind:disabled="!(driver.name != '' && driver.surname != '' && driver.passport != '' && driver.licenses != '' && driver.dateOfBirth != '')">
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
<script src="js/drivers.js"></script>
<?php include ('../footer.php')?>
</body>
</html>
