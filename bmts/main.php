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
    <title>Главная | BelDum</title>
</head>
<body>
<?php include ('nav.php')?>
<div class="container" id="main">
    <div class="row">
        <div class="col-md-8">
            <div class="card request-card w-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="d-block">Заявки</h5>
                    <button class="btn btn-sm btn-success" role="button" data-bs-toggle="modal" data-bs-target="#taskModal" @click="setTaskModalAdd">Создать заявку</button>
                </div>
                <table class="table table-bordered">
                    <thead>
                    <th class="text-center">Дата</th>
                    <th class="text-center">Задача</th>
                    <th class="text-center">Заказчик</th>
                    <th class="text-center">Организация</th>
                    <th class="text-center">Груз</th>
                    <th class="text-center">Действия</th>
                    </thead>
                    <tbody>
                        <tr v-if="!tasks && !emptyRequestsMessage">
                            <td class="text-center" colspan="6">Загрузка заявок...</td>
                        </tr>
                        <tr v-else-if="emptyRequestsMessage" v-cloak>
                            <td class="text-center" colspan="6">{{emptyRequestsMessage}}</td>
                        </tr>
                        <tr v-for="task in tasks" v-cloak>
                            <td class="text-center">{{task.date}}</td>
                            <td class="text-center">
                                <h5>
                                    <span v-if="task.state == 0" class="badge bg-success task-type-state" title="Статус: новая">{{task.task_type}}</span>
                                    <span v-else-if="task.state == 1" class="badge bg-primary task-type-state" title="Статус: в работе">{{task.task_type}}</span>
                                    <span v-else class="badge bg-secondary task-type-state" title="Статус: завершена">{{task.task_type}}</span>
                                </h5>
                            </td>
                            <td class="text-center">{{task.customer_surname}} {{task.customer_name}} {{task.customer_last_name}}</td>
                            <td class="text-center">{{task.organ_name}}</td>
                            <td class="text-center">{{task.cargo_name}}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        Действия
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" role="button" data-bs-toggle="modal" data-bs-target="#taskModal" @click="getTask(task.task_id)">Изменить</a></li>
                                        <li><a class="dropdown-item" role="button" @click="deleteTask(task.task_id)">Удалить</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
           <div class="card request-card">
               <div class="d-flex justify-content-between align-items-center mb-2">
                   <h5 class="d-block">Листы заданий</h5>
                   <?php if($_SESSION['role'] == 1) {echo '<button type="button" class="d-inline btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="setModalAdd">Создать лист заданий</button>';} ?>
               </div>
               <table class="table table-bordered">
                   <thead>
                   <tr>
                       <th class="text-center">Лист №</th>
                       <th class="text-center">Водитель</th>
                       <th class="text-center">Маршрут</th>
                       <th class="text-center">Действия</th>
                   </tr>
                   </thead>
                   <tbody>
                   <tr v-if="!task_lists && !emptyTableMessage">
                       <td class="text-center" colspan="4">Загрузка листов заданий...</td>
                   </tr>
                   <tr v-else-if="emptyTableMessage" v-cloak>
                       <td class="text-center" colspan="4">{{emptyTableMessage}}</td>
                   </tr>
                   <tr v-for="row in task_lists" v-cloak>
                       <td class="text-center">{{row.task_list_id}}</td>
                       <td class="text-center">{{row.initials}}</td>
                       <td class="text-center">{{row.route}}</td>
                       <td class="text-center">
                           <div class="dropdown">
                               <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                   Действия
                               </button>
                               <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                   <li><a class="dropdown-item" role="button" @click="getTaskListForEdit(row.task_list_id)" data-bs-toggle="modal" data-bs-target="#exampleModal">Изменить</a></li>
                                   <template v-if="user.role == 1">
                                       <li><a class="dropdown-item" role="button" @click="completeTaskList(row.task_list_id, row.tasks)">Завершить</a></li>
                                       <li><a class="dropdown-item" role="button" @click="deleteTaskList(row.task_list_id)">Удалить</a></li>
                                       <li><hr class="dropdown-divider"></li>
                                       <li><h6 class="dropdown-header">Доверенности</h6></li>
                                       <li><a role="button" class="dropdown-item" @click="getTaskListPOA(row.task_list_id)" data-bs-toggle="modal" data-bs-target="#poaModal">Создать</a></li>
                                   </template>
                                   <li><hr class="dropdown-divider"></li>
                                   <li><h6 class="dropdown-header">Экспорт</h6></li>
                                   <template v-if="user.permission_number != null">
                                       <li><a :href="'task-list.php?id='+row.task_list_id+'&action=1'" role="button" target="_blank" class="dropdown-item">Сохранить в PDF</a></li>
                                       <li><a :href="'task-list.php?id='+row.task_list_id+'&action=0'" role="button" target="_blank" class="dropdown-item">Посмотреть PDF</a></li>
                                   </template>
                                   <template v-else>
                                       <li><a href="settings.php" role="button" class="dropdown-item">Настроить</a></li>
                                   </template>
                               </ul>
                           </div>
                       </td>
                   </tr>
                   </tbody>
               </table>
           </div>
        </div>
    </div> <!-- table -->

    <!-- Modal list -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ modal.title }}</h5>
                    <input type="text" class="form-control task-list-number" v-model="task_list.task_list_id" maxlength="4" placeholder="0000" :readonly="modal.action == 'update'">
                    <button @click="clearFormData" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-auto">
                                    <label for="task-date" class="col-form-label">Дата</label>
                                </div>
                                <div class="col-auto">
                                    <input type="date" id="task-date" class="form-control" v-model="task_list.date">
                                </div>
                                <div class="col-auto">
                                    <label for="task-route" class="col-form-label">Маршрут</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="task-route" class="form-control" placeholder="Введите маршрут" autocomplete="off" v-model="task_list.route" maxlength="30">
                                </div>
                                <div class="col-auto">
                                    <label for="task-car" class="col-form-label">Автомобиль</label>
                                </div>
                                <div class="col-md-2">
                                    <select id="task-car" v-model="task_list.car_id" class="form-control task-list-car-input" @change="carChanged">
                                        <option value="" disabled>Выберите авто</option>
                                        <template v-if="cars">
                                            <option :value="row.car_id" v-for="row in cars">{{ row.mark }} {{ row.model }} ({{ row.number }})</option>
                                        </template>
                                        <option v-else value="" disabled>Загрузка...</option>
                                    </select>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-auto" v-if="task_list.car_id">
                                        <label for="" class="col-form-label">Водитель</label>
                                    </div>
                                    <div class="col-auto">
                                        <template v-for="row in cars" v-if="row.car_id == task_list.car_id">
                                            <div class="input-group">
                                                <input type="text" v-model="row.surname" class="form-control" readonly>
                                                <input type="text" v-model="row.name" class="form-control" readonly>
                                                <input type="text" v-model="row.last_name" class="form-control" readonly>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <table class="table table-bordered task-list-table">
                                    <thead>
                                    <tr>
                                        <th>№ п/п</th>
                                        <th>Адрес, наименование фирмы</th>
                                        <th>Контактное лицо на фирме, тел.</th>
                                        <th>Получать</th>
                                        <th>Товар получен по ТТН №</th>
                                        <th>Кон. лицо "заказчика"</th>
                                        <th>Приоритет</th>
                                        <th>Получатель</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(list_task, index) in task_list.tasks">
                                            <tr v-for="task in tasks" v-if="task.task_id == list_task.id">
                                                <td class="text-center">{{ index+1 }}</td>
                                                <td class="text-center">{{task.organ_name}}, {{task.organ_address}}</td>
                                                <td class="text-center">{{task.curator_organ}}, {{task.curator_organ_tel}}</td>
                                                <td class="text-center">{{task.cargo_name}}</td>
                                                <td class="text-center"></td>
                                                <td class="text-center">{{task.curator_remeza}}</td>
                                                <td class="text-center"></td>
                                                <td class="text-center">{{task.customer_surname}} {{task.customer_name}} {{task.customer_last_name}}</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="tasks-list card">
                                <p class="text-center p-0" v-if="!tasks && !emptyNewTasksMessage">Загрузка...</p>
                                <p class="text-center p-0" v-else-if="emptyNewTasksMessage" v-cloak>{{emptyNewTasksMessage}}</p>
                                <div class="card task-card" v-for="(task) in tasks">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5>Заявка #{{ task.task_id }}</h5><h6>Заказчик: {{task.customer_surname}} {{task.customer_name}} {{task.customer_last_name}}</h6>
                                    </div>
                                    <div class="task-card-row">
                                        <p class="task-card-title">Дата:</p><p>{{ task.date }}</p>
                                    </div>
                                    <div class="task-card-row">
                                        <p class="task-card-title">Вид перевозки:</p><p>{{ task.task_type }}</p>
                                    </div>
                                    <div class="task-card-row">
                                        <p class="task-card-title">Организация:</p><p>{{ task.organ_name }}</p>
                                    </div>
                                    <div class="task-card-row">
                                        <p class="task-card-title">Адрес:</p><p>{{ task.organ_address }}</p>
                                    </div>
                                    <div class="task-card-row">
                                        <p class="task-card-title">Груз:</p><p>{{ task.cargo_name }}</p>
                                    </div>
                                    <div class="task-card-row" v-if="task.cargo_l != '' || task.cargo_w != '' || task.cargo_h != ''">
                                        <p class="task-card-title">Габариты (ДхШхВ):</p><p class="task-card-text" v-if="task.cargo_l != '' && task.cargo_w != '' && task.cargo_h != ''">{{ task.cargo_l }} x {{ task.cargo_w }} x {{ task.cargo_h }} см.</p>
                                    </div>
                                    <div class="task-card-row" v-if="task.cargo_weight != ''">
                                        <p class="task-card-title">Вес:</p><p>{{ task.cargo_weight }}, кг.</p>
                                    </div>
                                    <div class="task-card-row" v-if="task.notes">
                                        <p class="task-card-title">Примечания:</p><p>{{ task.notes }}</p>
                                    </div>
                                    <button v-if="task.state == 2" class="btn btn-secondary btn-sm" disabled>Завершена</button>
                                    <template v-else>
                                        <button v-if="isInTaskList(task.task_id) == '-1'" class="btn btn-primary btn-sm" @click="addToTaskList(task.task_id)" >Добавить</button>
                                        <button v-else class="btn btn-danger btn-sm" @click="deleteFromTaskList(task.task_id)">Отменить</button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="clearFormData">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="modal.button" @click="modalRouter">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal task -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" v-cloak>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ taskModal.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearTaskForm"></button>
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
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="clearTaskForm">Отмена</button>
                    <input type="button" class="btn btn-success" v-model="taskModal.button" @click="taskModalRouter">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal POA -->
    <div class="modal fade" id="poaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Экспорт доверенностей</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clearFormData"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">Организация</th>
                            <th class="text-center">Груз</th>
                            <th class="text-center">Счет в доверенности</th>
                            <th class="text-center">Срок действия по</th>
                            <th class="text-center">№</th>
                            <th class="text-center">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr><td class="text-center" colspan="6" v-if="task_list.tasks.length == 0">Загрузка...</td></tr>
                            <template v-for="list_task in task_list.tasks">
                                <tr v-for="(task, index) in tasks" v-if="task.task_id == list_task.id">
                                    <td class="text-center pt-3">{{task.organ_name}}</td>
                                    <td class="text-center pt-3">{{task.cargo_name}}</td>
                                    <td class="text-center">
                                        <select v-model="poaData[index].bank_data" class="form-control">
                                            <option value="" disabled>Выберите реквизиты счета</option>
                                            <option value="" disabled v-show="!banks">Загрузка...</option>
                                            <option :value="'IBAN '+bank.bank_iban+' '+bank.bank_name+' '+bank.post_code+' '+bank.bank_address+', BIC - '+bank.bank_bic" v-for="bank in banks">{{bank.bank_name}} IBAN {{bank.bank_iban}}</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <input type="date" class="form-control" v-model="poaData[index].to_date">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control" v-model="poaData[index].number" placeholder="№ доверенности">
                                    </td>
                                    <td class="text-center">
                                        <template v-for="row in cars" v-if="row.car_id == task_list.car_id">
                                            <a v-if="poaData[index].bank_data && poaData[index].to_date && poaData[index].number" disabled target="_blank" class="btn btn-primary" role="button" :href="'power-of-attorney.php?driver_id='+row.driver_id+'&bank_data='+poaData[index].bank_data+'&date='+poaData[index].to_date+'&number='+poaData[index].number+'&organ_name='+task.organ_name+'&cargo_name='+task.cargo_name">Экспорт в PDF</a>
                                            <p v-else>Недостаточно данных</p>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <a href="settings.php" target="_blank" class="btn btn-primary d-block">Настройки</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Закрыть</button>
                    </div>
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
<script src="js/main.js"></script>
<?php include ('../footer.php')?>
</body>
</html>
