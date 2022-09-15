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
    <title>Организации | BelDum</title>
</head>
<body>
<?php include('nav.php') ?>
<div class="container" id="organization">
    <div class="card request-card w-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="d-block">Организации</h5>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Добавить организацию</button>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Название</th>
                    <th class="text-center">Адрес(а)</th>
                    <th class="text-center">Специализация</th>
                    <th class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!organs && !emptyTableMessage"><td class="text-center" colspan="4">Загрузка организаций...</td></tr>
                <tr v-else-if="emptyTableMessage"><td class="text-center" colspan="4">{{emptyTableMessage}}</td></tr>
                <tr v-for="row in organs" v-cloak>
                    <td class="text-center">{{ row.organ_name}}</td>
                    <td>
                        <ul class="mb-0">
                            <li v-for="address in row.organ_address">{{ address.region }} {{ address.city }} {{ address.street }}</li>
                        </ul>
                    </td>
                    <td class="text-center">{{ row.spec }}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                Действия
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><button class="dropdown-item" @click="getOrgan(row.organ_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</button></li>
                                <li><button class="dropdown-item" @click="deleteOrgan(row.organ_id)">Удалить</button></li>
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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Название организации</h6>
                            <input class="form-control" type="text" v-model="organ.organ_name" placeholder="Введите название организации" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <h6>Специализация</h6>
                            <input class="form-control" type="text" v-model="organ.spec" placeholder="Введите специализацию организации" maxlength="50">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Адреса организации</h6>
                        <button class="btn-sm btn btn-primary d-block" @click="addAddressInput">Добавить адрес</button>
                    </div>
                    <div v-for="(address, index) in organ.organ_address" class="mb-3">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6>Адрес #{{ index+1 }}</h6>
                            <button class="btn-danger btn btn-sm d-block" @click="deleteAddress(index)" v-if="organ.organ_address.length > 1">Удалить</button>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input class="form-control" v-model="address.country" type="text" placeholder="Введите страну" maxlength="30">
                            </div>
                            <div class="col-md-6">
                                <select v-if="address.country == 'Республика Беларусь'" class="form-control" v-model="address.region">
                                    <option value="" disabled>Выберите регион</option>
                                    <option value="Брестская обл.">Брестская обл.</option>
                                    <option value="Витебская обл.">Витебская обл.</option>
                                    <option value="Гомельская обл.">Гомельская обл.</option>
                                    <option value="Гродненская обл.">Гродненская обл.</option>
                                    <option value="Минская обл.">Минская обл.</option>
                                    <option value="Могилевская обл.">Могилевская обл.</option>
                                    <option value="г. Минск">г. Минск</option>
                                </select>
                                <input v-else class="form-control" type="text" placeholder="Введите название региона" v-model="address.region" maxlength="30">
                            </div>
                        </div>
                        <div class="input-group">
                            <input class="form-control" type="text" v-model="address.city" v-if="address.region != 'г. Минск'" placeholder="Введите название города" maxlength="30">
                            <input class="form-control" type="text" placeholder="Введите улицу и номер здания" v-model="address.street" maxlength="50">
                            <input class="form-control" type="text" placeholder="Введите почтовый индекс" v-model="address.postcode" maxlength="10">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter" :disabled="!(organ.organ_name != '' && organ.spec!= '' && organ.organ_address[0].value != '')">
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
<script src="js/organizations.js"></script>
<?php include ('../footer.php')?>
</body>
</html>