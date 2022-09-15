<?php
    session_start();
    include('../dbconn.php');
    if(!isset($_SESSION['role'])||(trim($_SESSION['role']) == '')) {
        header('location:index.php');
    }
    if($_SESSION['role'] != 1) {
        header('location:./access-denied.php');
    }
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
    <title>Настройки | BelDum</title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="settings">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Данные листа заданий</h5>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalPermission" :disabled="!permission && !noPermissionDataMessage">Изменить</button>
        </div>
        <div class="permission-example">
            <p><b>Пример из листа заданий:</b></p>
            <p class="text-center" v-if="!permission && !noPermissionDataMessage">Загрузка...</p>
            <p v-else-if="noPermissionDataMessage" class="text-center"><b v-if="noPermissionDataMessage">{{noPermissionDataMessage}}</b></p>
            <p v-cloak class="text-center" v-else>
                ...действующего на основании доверенности № <b>{{permission.permission_number}}</b> от <b>{{permission.permission_date}}</b>...
            </p>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Настройки экспорта доверенностей</h5>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalPoa" :disabled="!poa">Изменить</button>
        </div>
        <template v-if="!poa">
        <p class="text-center">Загрузка...</p>
        </template>
        <template v-else>
            <div class="row" v-cloak>
                <div class="col-md-6"><p class="text-end">Главный инженер</p></div>
                <div class="col-md-6">
                    <p class="text-start">
                        <b>{{poa.poa_eng}}</b>
                    </p>
                </div>
            </div>
            <div class="row" v-cloak>
                <div class="col-md-6"><p class="text-end">Главный бухгалтер</p></div>
                <div class="col-md-6">
                    <p class="text-start">
                        <b>{{poa.poa_acc}}</b>
                    </p>
                </div>
            </div>
        </template>
        <hr>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Банковские реквизиты ЗАО "Ремеза"</h5>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalBankData" :disabled="!poa">Изменить</button>
        </div>
        <template v-if="!poa">
            <p class="text-center">Загрузка...</p>
        </template>
        <template v-else-if="poa.bank_data.length > 0">
            <p class="text-center user-select-all" v-for="bank in poa.bank_data" v-cloak>
                IBAN <span class="text-uppercase">{{bank.bank_iban}}</span> {{bank.bank_name}} {{bank.post_code}} {{bank.bank_address}}, BIC - <span class="text-uppercase">{{bank.bank_bic}}</span>
            </p>
        </template>
        <p v-else class="text-center">Добавьте первый счет, чтобы увидеть его здесь!</p>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" v-cloak>{{modal.title}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearModal"></button>
                </div>
                <div class="modal-body" v-if="modal.action == 'permission'" v-cloak>
                    <div class="input-group">
                        <span class="input-group-text">Доверенность №</span>
                        <input type="text" class="form-control" v-model="permission_model.permission_number" placeholder="Введите №">
                        <span class="input-group-text">от</span>
                        <input type="date" class="form-control" v-model="permission_model.permission_date">
                    </div>
                </div>
                <div class="modal-body" v-else-if="modal.action == 'poa'" v-cloak>
                    <div class="row mb-2 d-flex align-items-center">
                        <div class="col-md-4 text-end">Гл. инженер</div>
                        <div class="col-md-8"><input type="text" class="form-control" maxlength="30" v-model="poa_model.poa_eng" placeholder="Введите фамилию и инициалы"></div>
                    </div>
                    <div class="row mb-2 d-flex align-items-center">
                        <div class="col-md-4 text-end">Гл. бухгалтер</div>
                        <div class="col-md-8"><input type="text" class="form-control" maxlength="30" v-model="poa_model.poa_acc" placeholder="Введите фамилию и инициалы"></div>
                    </div>
                </div>
                <div class="modal-body" v-else v-cloak>
                    <p class="text-center mb-0" v-if="poa_model.bank_data.length == 0">Добавьте первый счет, чтобы увидеть его здесь!</p>
                    <div v-for="(bank, index) in poa_model.bank_data" class="mb-3">
                        <hr v-if="index != 0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6>Счет #{{ index+1 }}</h6>
                            <button class="btn-danger btn btn-sm d-block" @click="deleteBankData(index)">Удалить</button>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Банк</span>
                            <input v-model="bank.bank_name" type="text" class="form-control" placeholder="Введите название банка">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Адрес</span>
                            <input v-model="bank.bank_address" type="text" class="form-control" placeholder="Введите адрес">
                            <input v-model="bank.post_code" type="text" class="form-control" placeholder="Введите индекс">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">IBAN</span>
                            <input v-model="bank.bank_iban" type="text" class="form-control input-uppercase" placeholder="Введите IBAN счета">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">BIC</span>
                            <input type="text" v-model="bank.bank_bic" class="form-control input-uppercase" placeholder="Введите BIC банка">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <button class="btn-sm btn btn-primary d-block" @click="addBankDataInput" v-if="modal.action == 'bank'">Добавить счет</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" @click="clearModal">Отмена</button>
                        <button type="button" class="btn btn-sm btn-success" @click="modalRouter" v-cloak>{{ modal.button }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="errorMessage" v-cloak>{{ errorMessage }}</div>
    <div v-if="successMessage" v-cloak>{{ successMessage }}</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="js/settings.js"></script>
<?php include ('../footer.php')?>
</body>
</html>
